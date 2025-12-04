@extends('layouts.app')

@section('title', 'Registrácia')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Registrácia</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Meno</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Vaše meno" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Váš email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Heslo" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Potvrdenie hesla</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Zopakujte heslo" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Registrovať sa</button>
                    </form>
                    <p class="mt-3 text-center">
                        Už máte účet? <a href="{{ route('login') }}" class="custom-btn">Prihlásiť sa</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

