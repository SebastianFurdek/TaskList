@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $project->name }}</h1>
            <p class="text-muted small mb-0">Detaily projektu</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-light">Upraviť</a>
            <form action="{{ route('projects.destroy', $project->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Zmazať</button>
            </form>
        </div>
    </div>

    <div class="card p-4">
        <p class="mb-0">{{ $project->description ?? 'Bez popisu' }}</p>
    </div>
</div>
@endsection

