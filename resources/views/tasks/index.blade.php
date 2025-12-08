@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1">Task Manager</h1>
                <hr class="mt-0" style="height:2px; border:none; background:#e9ecef;">
                <p class="text-muted small mb-0">Prehľad vašich úloh. Označte dokončené alebo spravujte ich.</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <form method="GET" action="{{ route('tasks.index') }}" class="d-flex">
                    <input name="q" type="search" class="form-control form-control-sm me-2" placeholder="Hľadať úlohy...">
                </form>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm text-white">+ Nová úloha</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($tasks->isEmpty())
            <div class="card p-4 text-center">
                <p class="mb-0">Nemáte žiadne úlohy. Vytvorte si prvú úlohu kliknutím na tlačidlo <strong>Nová úloha</strong>.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($tasks as $task)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="card-title mb-1">{{ Str::limit($task->title, 60) }}</h5>
                                        @if($task->due_date)
                                            <small class="text-muted">Termín: {{ $task->due_date->format('Y-m-d') }}</small>
                                        @else
                                            <small class="text-muted">Termín: -</small>
                                        @endif
                                    </div>
                                    <div>
                                        @if($task->completed)
                                            <span class="badge bg-success">Dokončené</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="card-text text-muted mb-3">{{ Str::limit($task->description, 120) }}</p>

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-primary btn-sm">Zobraziť</a>
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-outline-secondary btn-sm">Upraviť</a>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Zmazať</button>
                                        </form>

                                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST" style="display:inline;" class="complete-form">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Dokončiť</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
