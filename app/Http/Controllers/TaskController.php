<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\Category;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // base query for user's tasks
        $query = Task::where('user_id', $userId)->with('project')->orderBy('due_date');

        // optional search
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            });
        }

        // optional project filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        $tasks = $query->get();

        // load user's projects for the filter select
        $projects = Project::where('user_id', $userId)->get();

        // If AJAX, return rendered partial HTML for the tasks list
        if ($request->ajax()) {
            $html = view('tasks._list', compact('tasks'))->render();
            return response()->json(['html' => $html]);
        }

        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // pass user's projects for assignment
        $projects = Project::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        return view('tasks.create', compact('projects', 'categories'));
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
            'project_id' => 'nullable|integer|exists:projects,id',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        // if project_id provided, ensure the project belongs to the user
        if (!empty($data['project_id'])) {
            $project = Project::where('id', $data['project_id'])->where('user_id', auth()->id())->first();
            if (! $project) {
                return back()->withErrors(['project_id' => 'Neplatný projekt'])->withInput();
            }
        }

        // ensure provided categories belong to the user
        $categoryIds = [];
        if (!empty($data['categories'])) {
            $valid = Category::whereIn('id', $data['categories'])->where('user_id', auth()->id())->pluck('id')->toArray();
            $categoryIds = $valid;
        }

        $data['user_id'] = auth()->id();
        $data['completed'] = isset($data['completed']) && $data['completed'] ? 1 : 0;

        $task = Task::create($data);

        if (!empty($categoryIds)) {
            $task->categories()->sync($categoryIds);
        }

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
        $projects = Project::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        return view('tasks.edit', compact('task','projects','categories'));
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
            'project_id' => 'nullable|integer|exists:projects,id',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        if (!empty($data['project_id'])) {
            $project = Project::where('id', $data['project_id'])->where('user_id', auth()->id())->first();
            if (! $project) {
                return back()->withErrors(['project_id' => 'Neplatný projekt'])->withInput();
            }
        }

        // ensure provided categories belong to the user
        $categoryIds = [];
        if (!empty($data['categories'])) {
            $valid = Category::whereIn('id', $data['categories'])->where('user_id', auth()->id())->pluck('id')->toArray();
            $categoryIds = $valid;
        }

        $data['completed'] = isset($data['completed']) && $data['completed'] ? 1 : 0;

        $task->update($data);

        // sync categories (if none provided, detach)
        $task->categories()->sync($categoryIds);

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
    public function complete(Request $request, $id)
    {
        // find task belonging to current user
        $task = Task::where('id', $id)->where('user_id', auth()->id())->first();
        if (! $task) {
            // idempotent: if task is already deleted or not accessible, treat as success for AJAX clients
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Úloha už spracovaná alebo neexistuje.'], 200);
            }
            return redirect()->route('tasks.index')->with('success', 'Úloha už spracovaná alebo neexistuje.');
        }

        // increment user's completed_tasks_count
        $user = $task->user;
        $user->increment('completed_tasks_count');

        $task->delete();

        if ($request->wantsJson() || $request->ajax()) {
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
