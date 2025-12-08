<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $projects_count = \App\Models\Project::where('user_id', $userId)->count();
        $tasks_count = \App\Models\Task::where('user_id', $userId)->count();
        $completed_tasks_count = optional(\App\Models\User::find($userId))->completed_tasks_count ?? 0;


        return view('dashboard', compact('projects_count', 'tasks_count', 'completed_tasks_count'));
    }

}
