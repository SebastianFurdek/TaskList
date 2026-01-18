@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Upraviť projekt</h1>
            <p class="text-muted small mb-0">Upravte nastavenia projektu.</p>
        </div>

        <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm">Späť</a>
    </div>

    <div class="card p-4">
        <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Názov</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Popis</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Prílohy (pridajte nové, predchádzajúce sú zachované)</label>
                <input type="file" name="attachments[]" class="form-control" multiple>
                @error('attachments.*')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Uložiť</button>
            </div>
        </form>
    </div>

    {{-- existujúce prílohy --}}
    <div class="card p-4 mt-3">
        <h5 class="mb-3">Existujúce prílohy</h5>
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
