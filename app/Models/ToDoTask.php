<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoTask extends Model
{
    protected $table = 'todo_tasks';

    protected $fillable = [
        'title',
        'description',
        'due_at',
        'status',
        'completed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
