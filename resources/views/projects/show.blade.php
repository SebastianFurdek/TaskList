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

    <div class="card p-4 mt-3">
        <h5 class="mb-3">Prílohy</h5>

        {{-- upload form --}}
        <form action="{{ route('projects.attachments.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <div class="input-group">
                <input type="file" name="attachment" class="form-control form-control-sm" required>
                <button class="btn btn-primary btn-sm" type="submit">Nahráť</button>
            </div>
            @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </form>

        {{-- list attachments --}}
        @if($project->attachments && $project->attachments->count())
            <ul class="list-unstyled mb-0">
                @foreach($project->attachments as $att)
                    <li class="d-flex align-items-center py-2 border-top">
                        <a href="{{ route('projects.attachments.download', [$project->id, $att->id]) }}" class="me-3">{{ $att->filename }}</a>
                        <small class="text-muted ms-auto">{{ number_format($att->size/1024, 1) }} KB</small>
                        <form action="{{ route('projects.attachments.destroy', [$project->id, $att->id]) }}" method="POST" class="ms-3">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Zmazať</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="small text-muted">Žiadne prílohy.</div>
        @endif
    </div>
</div>
@endsection
