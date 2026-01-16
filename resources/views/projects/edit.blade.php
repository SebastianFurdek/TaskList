@extends('layouts.app')
@endsection
</div>
    </div>
        </form>
            </div>
                <button class="btn btn-primary">Uložiť</button>
            <div class="d-flex justify-content-end">

            </div>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                <label class="form-label">Popis</label>
            <div class="mb-3">

            </div>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                <label class="form-label">Názov</label>
            <div class="mb-3">

            @method('PUT')
            @csrf
        <form action="{{ route('projects.update', $project->id) }}" method="POST">
    <div class="card p-4">

    </div>
        <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm">Späť</a>

        </div>
            <p class="text-muted small mb-0">Upravte nastavenia projektu.</p>
            <h1 class="h3 mb-1">Upraviť projekt</h1>
        <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
<div class="container py-4">
@section('content')


