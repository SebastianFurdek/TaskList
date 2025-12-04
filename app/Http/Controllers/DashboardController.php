<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        //$tasks_count = auth()->user()->tasks()->count();
        //$projects_count = auth()->user()->projects()->count();
        //$categories_count = auth()->user()->categories()->count();
        //$latest_tasks = auth()->user()->tasks()->latest()->take(5)->get();

        return view('dashboard', /*compact('tasks_count', 'projects_count', 'categories_count', 'latest_tasks')*/);
    }

}
