@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Nový projekt</h1>
            <p class="text-muted small mb-0">Vytvorte nový projekt.</p>
        </div>

        <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm">Späť</a>
    </div>

    <div class="card p-4">
        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Názov</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Popis</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Prílohy (voliteľné)</label>
                <input type="file" name="attachments[]" class="form-control" multiple>
                @error('attachments.*')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Vytvoriť</button>
            </div>
        </form>
    </div>
</div>
@endsection
