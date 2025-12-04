<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // cesta k tvojmu login.blade.php
    }

    public function showRegisterForm()
    {
        return view('auth.register'); // cesta k tvojmu register.blade.php
    }
}
