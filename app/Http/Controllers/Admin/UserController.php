<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Show all users
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $current = Auth::user();

        if ($current && $user->getKey() === $current->getKey()) {
            return redirect()->route('admin.users.index')->with('error', 'Administrátor si nemôže zmazať sám seba.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Používateľ bol zmazaný.');
    }
}

