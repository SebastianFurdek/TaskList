@extends('layouts.app')

@section('title', 'Prihlásenie')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Prihlásenie</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Váš email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Heslo" required>
                        </div>
                        <button type="submit" class="custom-btn">Prihlásiť sa</button>
                    </form>
                    <p class="mt-3 text-center">
                        Nemáte účet? <a href="{{ route('register') }}">Registrovať sa</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

