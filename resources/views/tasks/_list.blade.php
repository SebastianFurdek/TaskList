@if($tasks->isEmpty())
    <div class="card p-4 text-center">
        <p class="mb-0">Nemáte žiadne úlohy. Vytvorte si prvú úlohu kliknutím na tlačidlo <strong>Nová úloha</strong>.</p>
    </div>
@else
    <div class="list-group" id="tasks-list">
        @foreach($tasks as $task)
            <div class="list-group-item d-flex align-items-center py-2" data-task-id="{{ $task->id }}">
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="me-2 complete-form">
                    @csrf
                    <input type="checkbox" class="form-check-input complete-checkbox" {{ $task->completed ? 'checked disabled' : '' }} title="Označiť ako dokončené">
                </form>

                <div class="flex-grow-1 ms-1 me-3" style="min-width:0;">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('tasks.show', $task->id) }}" class="mb-0 text-decoration-none fw-semibold text-truncate {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}" style="max-width:100%;">{{ $task->title }}</a>
                        @if($task->project)
                            <span class="badge bg-secondary ms-2 small">{{ $task->project->name }}</span>
                        @endif
                    </div>
                    <div class="small text-muted text-truncate" style="max-width:100%;">{{ Str::limit($task->description, 80) }}</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="small text-muted">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') : '—' }}</div>
                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-light p-1" title="Upraviť">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2L3 10.207V13h2.793L14 4.793 11.207 2z"/>
                        </svg>
                    </a>

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Naozaj chcete túto úlohu zmazať?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm ms-1" aria-label="Zmazať"><span aria-hidden="true">&times;</span></button>
                    </form>
                </div>
            </div>
            @if(! $loop->last)
                <hr class="my-1" />
            @endif
        @endforeach
    </div>
@endif

