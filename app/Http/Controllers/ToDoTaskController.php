<?php

namespace App\Http\Controllers;

use App\Models\ToDoTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ToDoTaskController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_at' => ['required', 'date'],
        ]);

        ToDoTask::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Task added successfully.');
    }

    public function updateStatus(ToDoTask $task): RedirectResponse
    {
        $completed = $task->status !== 'completed';

        $task->update([
            'status' => $completed ? 'completed' : 'pending',
            'completed_at' => $completed ? now() : null,
        ]);

        return back()->with('success', $completed ? 'Task marked as completed.' : 'Task moved to pending.');
    }
}
