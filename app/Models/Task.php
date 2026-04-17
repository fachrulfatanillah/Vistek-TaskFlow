<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'description',
        'status',
        'user_id',
    ];

    /**
     * Relations (BelongsTo).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
