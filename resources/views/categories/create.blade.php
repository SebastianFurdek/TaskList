@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Vytvoriť kategóriu</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Názov</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Farba</label>
            <input type="text" name="color" id="color" class="form-control" value="{{ old('color') }}" placeholder="#ff0000">
        </div>

        <button class="btn btn-primary">Vytvoriť</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Späť</a>
    </form>
</div>
@endsection

