@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil</h1>

    <form method="POST" action="/profile">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label">Meno</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <button class="btn btn-primary" type="submit">Uložiť</button>
    </form>

    <hr />

    <form method="POST" action="/profile" onsubmit="return confirm('Naozaj chcete zmazať účet?');">
        @csrf
        @method('DELETE')
        <div class="mb-3">
            <label class="form-label">Potvrďte heslo pre zmazanie účtu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-danger" type="submit">Zmazať účet</button>
    </form>
</div>
@endsection

