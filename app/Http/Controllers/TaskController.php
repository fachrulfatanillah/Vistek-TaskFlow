<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $query = Task::with('user');

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by User
        if ($request->filled('user_id')) {
            if ($request->user_id === 'unassigned') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        $tasks = $query->latest()->get();
        return view('tasks.index', compact('tasks', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Ambil user yang paling sedikit task sebelum create task
            $eligibleUser = User::withCount(['tasks' => function ($query) {
                $query->whereIn('status', ['pending', 'in progress']);
            }])
                ->having('tasks_count', '<', 3)
                ->orderBy('tasks_count', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            // 2. Buat Task
            Task::create([
                'description' => $request->description,
                'status' => 'pending',
                'user_id' => $eligibleUser ? $eligibleUser->id : null,
            ]);
        });

        return redirect()->back();
    }

    public function updateStatus(Task $task)
    {
        if ($task->status === 'pending') {
            $task->update(['status' => 'in progress']);
        } elseif ($task->status === 'in progress') {
            DB::transaction(function () use ($task) {
                // Set task menjadi done
                $task->update(['status' => 'done']);

                // 2. melakukan pengecekan apakah ada task yang masih 'Unassigned' & 'Pending'
                $unassignedTask = Task::whereNull('user_id')
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($unassignedTask) {
                    // 3. Cek beban kerja user sekarang
                    $activeTasksCount = Task::where('user_id', $task->user_id)
                        ->whereIn('status', ['pending', 'in progress'])
                        ->count();

                    // 4. Jika slot user ini masih ada, berikan task unassigned
                    if ($activeTasksCount < 3) {
                        $unassignedTask->update(['user_id' => $task->user_id]);
                    }
                }
            });
        }

        return redirect()->back();
    }
}
