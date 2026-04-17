<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/user-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task-style.css') }}">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Task Assignment</h2>
    </x-slot>

    <div class="task-container">
        <div class="header-section">
            <h3 style="font-size: 1.2rem; font-weight: 600; color: #1e293b;">Task List</h3>
            <button class="btn-add" onclick="openModal()">Create Task</button>
        </div>

        <form action="{{ route('tasks.index') }}" method="GET" class="filter-card">
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in progress" {{ request('status') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="filter-group">
                <label>User</label>
                <select name="user_id">
                    <option value="">All Users</option>
                    <option value="unassigned" {{ request('user_id') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-add" style="padding: 9px 20px;">Filter</button>
            <a href="{{ route('tasks.index') }}" style="font-size: 0.8rem; color: #64748b;">Reset</a>
        </form>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->description }}</td>
                    <td>
                        <span class="badge badge-{{ $task->status == 'in progress' ? 'progress' : $task->status }}">
                            {{ $task->status }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $task->user->name ?? 'Unassigned' }}</strong>
                    </td>
                    <td style="text-align: right;">
                        @if($task->status != 'done')
                        <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-status">
                                {{ $task->status == 'pending' ? 'Start Working' : 'Mark as Done' }}
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-row">No tasks found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Create New Task</h2>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px;" required></textarea>
                </div>
                <button type="submit" class="btn-save">Create & Auto-Assign</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById("userModal").style.display = "block"; }
        function closeModal() { document.getElementById("userModal").style.display = "none"; }
    </script>
</x-app-layout>