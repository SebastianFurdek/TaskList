<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // load projects for the user and eager-load only that user's tasks
        $projects = \App\Models\Project::where('user_id', auth()->id())
            ->with(['tasks' => function($q) { $q->where('user_id', auth()->id())->orderBy('due_date'); }])
            ->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // accept attachments if present (single or multiple). Keep attachments nullable so single uploads don't fail.
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,txt',
            'attachments' => 'nullable',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,txt',
        ]);

        $data['user_id'] = auth()->id();

        // remove attachments from data so mass-assignment doesn't try to write them to Project
        if (array_key_exists('attachments', $data)) {
            unset($data['attachments']);
        }

        $project = Project::create($data);

        // handle attachments if any (support single or multiple uploads)
        try {
            $files = [];
            if ($request->hasFile('attachments')) {
                $af = $request->file('attachments');
                if (is_array($af)) $files = array_merge($files, $af); else $files[] = $af;
            }
            if ($request->hasFile('attachment')) {
                $single = $request->file('attachment');
                if ($single) $files[] = $single;
            }

            if ($files) {
                foreach ($files as $file) {
                    if (! $file || ! $file->isValid()) continue;
                    $path = $file->store("project-attachments/{$project->id}", 'public');
                    $att = new \App\Models\ProjectAttachment();
                    $att->filename = $file->getClientOriginalName();
                    $att->path = $path;
                    $att->mime = $file->getClientMimeType();
                    $att->size = $file->getSize();
                    $project->attachments()->save($att);
                }
            }
        } catch (\Throwable $e) {
            // if attachments table/migration missing or disk error, log and continue
            logger()->error('Project attachments save failed: ' . $e->getMessage());
            session()->flash('warning', 'Projekt vytvorený, ale prílohy sa nepodarilo uložiť (spustite migrácie alebo skontrolujte storage).');
        }

        return redirect()->route('projects.index')->with('success', 'Projekt bol vytvorený.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // ensure the user owns the project
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachments' => 'nullable',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,txt',
        ]);

        // prevent attachments array from being mass-assigned to project
        if (array_key_exists('attachments', $data)) {
            unset($data['attachments']);
        }
        $project->update($data);

        // handle attachments if any (append) - robust & non-fatal
        try {
            $files = [];
            if ($request->hasFile('attachments')) {
                $af = $request->file('attachments');
                if (is_array($af)) $files = array_merge($files, $af); else $files[] = $af;
            }
            if ($request->hasFile('attachment')) {
                $single = $request->file('attachment');
                if ($single) $files[] = $single;
            }

            if ($files) {
                foreach ($files as $file) {
                    if (! $file || ! $file->isValid()) continue;
                    $path = $file->store("project-attachments/{$project->id}", 'public');
                    $att = new \App\Models\ProjectAttachment();
                    $att->filename = $file->getClientOriginalName();
                    $att->path = $path;
                    $att->mime = $file->getClientMimeType();
                    $att->size = $file->getSize();
                    $project->attachments()->save($att);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Project attachments save failed (update): ' . $e->getMessage());
            session()->flash('warning', 'Projekt upravený, ale niektoré prílohy sa nepodarilo uložiť.');
        }

        return redirect()->route('projects.index')->with('success', 'Projekt bol upravený.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projekt bol zmazaný.');
    }
}
