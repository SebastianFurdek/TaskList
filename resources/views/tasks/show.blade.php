@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h1 class="h4 mb-1">{{ $task->title }}</h1>
                        <p class="text-muted mb-0">{{ $task->description ?: 'Žiadny popis.' }}</p>
                    </div>

                    <div class="text-end">
                        @if($task->completed)
                            <span class="badge bg-success">Dokončené</span>
                        @else
                            <span class="badge bg-warning text-dark">Prebieha</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <small class="text-muted">Termín</small>
                        <div>
                            @php
                                $due = null;
                                if ($task->due_date) {
                                    try {
                                        $due = \Carbon\Carbon::parse($task->due_date)->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        $due = (string) $task->due_date;
                                    }
                                }
                            @endphp

                            <strong>{{ $due ?? 'Neurčený' }}</strong>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <small class="text-muted">Vytvorené</small>
                        <div>
                            <strong>{{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i') : '–' }}</strong>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <small class="text-muted">Autor</small>
                        <div>
                            <strong>{{ $task->user?->name ?? '–' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">Upraviť</a>
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Späť</a>

                    @unless($task->completed)
                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Označiť ako dokončené</button>
                        </form>
                    @endunless

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Naozaj chcete túto úlohu zmazať?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Zmazať</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
