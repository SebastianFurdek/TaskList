@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1">Projekty</h1>
                <p class="text-muted small mb-0">Zoznam vašich projektov.</p>
            </div>

            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm text-white">
                + Nový projekt
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($projects->isEmpty())
            <div class="card p-4 text-center">
                <p class="mb-0">Nemáte žiadne projekty. Vytvorte si prvý projekt.</p>
            </div>
        @else
            <div class="list-group">
                @foreach($projects as $project)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold mb-1">{{ $project->name }}</div>
                                <div class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($project->description, 120) }}</div>

                                {{-- nested tasks for this project --}}
                                @if($project->tasks && $project->tasks->count())
                                    <div class="mt-2">
                                        <div class="small text-muted mb-1">Úlohy v tomto projekte:</div>
                                        <div class="list-group list-group-flush">
                                            @foreach($project->tasks as $ptask)
                                                <div class="list-group-item d-flex align-items-center py-1 px-0">
                                                    <form action="{{ route('tasks.complete', $ptask->id) }}" method="POST" class="me-2 complete-form">
                                                        @csrf
                                                        <input type="checkbox" class="form-check-input complete-checkbox" {{ $ptask->completed ? 'checked disabled' : '' }}>
                                                    </form>

                                                    <div class="flex-grow-1 ms-2 me-3" style="min-width:0;">
                                                        <a href="{{ route('tasks.show', $ptask->id) }}" class="text-decoration-none text-truncate {{ $ptask->completed ? 'text-decoration-line-through text-muted' : '' }}">{{ $ptask->title }}</a>
                                                        <div class="small text-muted">{{ $ptask->due_date ? \Carbon\Carbon::parse($ptask->due_date)->format('d.m.Y') : '—' }}</div>
                                                    </div>

                                                    <div class="d-flex gap-2 align-items-center">
                                                        <a href="{{ route('tasks.edit', $ptask->id) }}" class="btn btn-sm btn-light p-1" title="Upraviť">
                                                            <!-- pencil small -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16" aria-hidden="true">
                                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l2.0 2.0a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-4 1a.5.5 0 0 1-.63-.63l1-4a.5.5 0 0 1 .11-.168l10-10zM11.207 2L3 10.207V13h2.793L14 4.793 11.207 2z"/>
                                                            </svg>
                                                        </a>

                                                        <form action="{{ route('tasks.destroy', $ptask->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Naozaj chcete túto úlohu zmazať?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" aria-label="Zmazať">&times;</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2"><div class="small text-muted">Žiadne úlohy v tomto projekte.</div></div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-light">Upraviť</a>

                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Naozaj chcete tento projekt zmazať?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Zmazať</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
