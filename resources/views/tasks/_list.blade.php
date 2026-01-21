@if($tasks->isEmpty())
    <div class="card p-4 text-center">
        <p class="mb-0">Nemáte žiadne úlohy. Vytvorte si prvú úlohu kliknutím na tlačidlo <strong>Nová úloha</strong>.</p>
    </div>
@else
    <div class="" id="tasks-list">
        @foreach($tasks as $task)
            <div class="task-item d-flex align-items-center" data-task-id="{{ $task->id }}" data-complete-url="{{ route('tasks.complete', $task->id) }}">
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="me-3 complete-form">
                    @csrf
                    <input type="checkbox" class="form-check-input complete-checkbox" {{ $task->completed ? 'checked disabled' : '' }} title="Označiť ako dokončené">
                </form>

                <div class="flex-grow-1 ms-1 me-3" style="min-width:0;">
                    <div class="d-flex align-items-center flex-wrap">
                        <a href="{{ route('tasks.show', $task->id) }}" class="mb-0 text-decoration-none fw-semibold text-truncate {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}" style="max-width:100%;">{{ $task->title }}</a>

                        @if($task->project)
                            <span class="badge bg-secondary ms-2 small">{{ $task->project->name }}</span>
                        @endif

                        {{-- category badges (if present) --}}
                        @if(isset($task->categories) && $task->categories->count())
                            <div class="ms-2 d-flex flex-wrap gap-1">
                                @foreach($task->categories as $cat)
                                    @php $bg = $cat->color ?? '#6c757d'; @endphp
                                    <span class="category-badge small" style="background: {{ $bg }};">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="small text-muted text-truncate mt-1" style="max-width:100%;">{{ Str::limit($task->description, 80) }}</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="small text-muted text-end me-2" style="min-width:70px;">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') : '—' }}</div>

                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-light p-1 d-inline-flex align-items-center" title="Upraviť">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2L3 10.207V13h2.793L14 4.793 11.207 2z"/>
                        </svg>
                    </a>

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Naozaj chcete túto úlohu zmazať?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm ms-1" aria-label="Zmazať"><span aria-hidden="true">&times;</span></button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
