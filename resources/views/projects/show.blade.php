@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $project->name }}</h1>
            <p class="text-muted small mb-0">Detaily projektu</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">Späť</a>
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-light">Upraviť</a>
            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Naozaj chcete tento projekt zmazať?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Zmazať</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Popis</h5>
            <p class="card-text">{{ $project->description ?? 'Bez popisu' }}</p>
        </div>
    </div>

    {{-- Tasks (full-width, stacked) --}}
    <div class="card mb-4">
        <div class="card-body">


            @if($project->tasks && $project->tasks->count())
                <div class="list-group list-group-flush">
                    @foreach($project->tasks as $ptask)
                        <div class="list-group-item d-flex align-items-center py-2" data-task-id="{{ $ptask->id }}" data-complete-url="{{ route('tasks.complete', $ptask->id) }}">
                            <form action="{{ route('tasks.complete', $ptask->id) }}" method="POST" class="me-2 complete-form">
                                @csrf
                                <input type="checkbox" class="form-check-input complete-checkbox" {{ $ptask->completed ? 'checked disabled' : '' }} title="Označiť ako dokončené">
                            </form>

                            <div class="flex-grow-1 ms-2 me-3" style="min-width:0;">
                                <a href="{{ route('tasks.show', $ptask->id) }}" class="mb-0 text-decoration-none text-truncate {{ $ptask->completed ? 'text-decoration-line-through text-muted' : '' }}" style="max-width:100%;">{{ $ptask->title }}</a>
                                <div class="small text-muted">{{ $ptask->due_date ? \Carbon\Carbon::parse($ptask->due_date)->format('d.m.Y') : '—' }}</div>
                                <div class="small text-muted mt-1">{{ Str::limit($ptask->description, 120) }}</div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('tasks.edit', $ptask->id) }}" class="btn btn-sm btn-light p-1" title="Upraviť">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2L3 10.207V13h2.793L14 4.793 11.207 2z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('tasks.destroy', $ptask->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Naozaj chcete túto úlohu zmazať?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm ms-1" aria-label="Zmazať"><span aria-hidden="true">&times;</span></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card p-3 text-center">
                    <p class="mb-0 text-muted">Žiadne úlohy v tomto projekte.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Attachments (stacked below tasks) --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Prílohy</h5>

            <form action="{{ route('projects.attachments.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="file" name="attachment" class="form-control form-control-sm">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Nahráť</button>
                </div>
                @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </form>

            @if($project->attachments && $project->attachments->count())
                <ul class="list-unstyled mb-0">
                    @foreach($project->attachments as $att)
                        <li class="mb-2 d-flex align-items-center">
                            <a href="{{ route('projects.attachments.download', [$project->id, $att->id]) }}">{{ $att->filename }}</a>
                            <small class="text-muted ms-2">{{ number_format($att->size/1024, 1) }} KB</small>
                            <form action="{{ route('projects.attachments.destroy', [$project->id, $att->id]) }}" method="POST" class="ms-auto">
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
</div>
@endsection
