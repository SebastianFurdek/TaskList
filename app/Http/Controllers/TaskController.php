<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->get();
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'completed' => 'sometimes|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['completed'] = isset($data['completed']) && $data['completed'] ? 1 : 0;

        $task = Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Úloha vytvorená.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->ensureOwnership($task);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->ensureOwnership($task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->ensureOwnership($task);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'completed' => 'sometimes|boolean',
        ]);

        $data['completed'] = isset($data['completed']) && $data['completed'] ? 1 : 0;

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Úloha upravená.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->ensureOwnership($task);
        $task->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Úloha zmazaná.']);
        }

        return redirect()->route('tasks.index')->with('success', 'Úloha zmazaná.');
    }

    /**
     * Remove the specified resource from storage (mark complete -> delete).
     */
    public function complete(Task $task)
    {
        $this->ensureOwnership($task);

        // increment user's completed_tasks_count
        $user = $task->user;
        $user->increment('completed_tasks_count');

        $task->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Úloha dokončená a zmazaná.']);
        }

        return redirect()->route('tasks.index')->with('success', 'Úloha dokončená a zmazaná.');
    }

    protected function ensureOwnership(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
