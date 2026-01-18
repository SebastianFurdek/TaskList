<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectAttachment;
use Illuminate\Support\Facades\Storage;

class ProjectAttachmentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) abort(403);

        $request->validate([
            'attachment' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,txt'
        ]);

        $file = $request->file('attachment');
        $path = $file->store("project-attachments/{$project->id}", 'public');

        // avoid mass-assignment static analysis warnings: create model via new + save on relationship
        $attachment = new ProjectAttachment();
        $attachment->filename = $file->getClientOriginalName();
        $attachment->path = $path;
        $attachment->mime = $file->getClientMimeType();
        $attachment->size = $file->getSize();
        $project->attachments()->save($attachment);

        return back()->with('success', 'Príloha nahratá.');
    }

    public function download(Project $project, ProjectAttachment $attachment)
    {
        if ($project->user_id !== auth()->id()) abort(403);
        if ($attachment->project_id !== $project->id) abort(404);

        return Storage::disk('public')->download($attachment->path, $attachment->filename);
    }

    public function destroy(Project $project, ProjectAttachment $attachment)
    {
        if ($project->user_id !== auth()->id()) abort(403);
        if ($attachment->project_id !== $project->id) abort(404);

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Príloha zmazaná.');
    }
}
