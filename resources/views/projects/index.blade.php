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
            <div class="">
                @foreach($projects as $project)
                    <div class="project-item">
                        <div class="d-flex justify-content-between align-items-start w-100">
                            <div>
                                <div class="project-title mb-1">{{ $project->name }}</div>
                                <div class="project-meta small mb-2">{{ \Illuminate\Support\Str::limit($project->description, 120) }}</div>

                                {{-- nested tasks for this project --}}
                                @if($project->tasks && $project->tasks->count())
                                    <div class="mt-2">
                                        <div class="small text-muted mb-1">Úlohy v tomto projekte:</div>
                                        <div class="list-group list-group-flush">
                                            @foreach($project->tasks as $ptask)
                                                <div class="d-flex align-items-center py-1 px-0 task-item-nested" data-task-id="{{ $ptask->id }}" data-complete-url="{{ route('tasks.complete', $ptask->id) }}">
                                                    <form action="{{ route('tasks.complete', $ptask->id) }}" method="POST" class="me-2 complete-form">
                                                        @csrf
                                                        <input type="checkbox" class="form-check-input complete-checkbox" {{ $ptask->completed ? 'checked disabled' : '' }} title="Označiť ako dokončené">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = csrfMeta ? csrfMeta.getAttribute('content') : null;

            const doPost = (url, form) => {
                return new Promise((resolve, reject) => {
                    const params = new URLSearchParams();
                    const fd = new FormData(form);
                    for (const pair of fd.entries()) params.append(pair[0], pair[1]);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', url);
                    xhr.withCredentials = true;
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');
                    if (csrf) xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
                    xhr.onload = function () { resolve({ ok: xhr.status >= 200 && xhr.status < 400, status: xhr.status, text: () => Promise.resolve(xhr.responseText), url: url }); };
                    xhr.onerror = function (e) { reject(e); };
                    xhr.send(params.toString());
                });
            };

            document.querySelectorAll('.task-item-nested .complete-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', async function () {
                    if (this.disabled) return;
                    const form = this.closest('form.complete-form');
                    if (!form) return;
                    const row = this.closest('.task-item-nested');
                    const taskId = row ? row.getAttribute('data-task-id') : null;
                    const dataUrl = row ? row.getAttribute('data-complete-url') : null;
                    this.disabled = true;

                    try {
                        let url = dataUrl || form.action;
                        let res = await doPost(url, form);
                        if (res.status === 404 && taskId) {
                            const altPath = '/tasks/' + taskId + '/complete';
                            res = await doPost(altPath, form);
                        }

                        if (res.ok || res.status === 404) {
                            // remove row from DOM on success or 404 (already gone)
                            if (row) {
                                row.style.transition = 'opacity .25s ease, transform .25s ease';
                                row.style.opacity = '0';
                                row.style.transform = 'translateY(-8px)';
                                setTimeout(function () { row.remove(); }, 300);
                            } else {
                                window.location.reload();
                            }
                        } else {
                            this.checked = false;
                            this.disabled = false;
                            let text = await res.text().catch(() => null);
                            let msg = `Pri označovaní úlohy nastala chyba: Server error ${res.status}`;
                            if (res.status === 401 || res.status === 419) msg += ' — nie ste prihlásený alebo vypršal CSRF token.';
                            if (text) {
                                try { const j = JSON.parse(text); if (j && j.message) msg += `: ${j.message}`; else msg += `: ${text.substring(0,300)}`; } catch (_) { msg += `: ${text.substring(0,300)}`; }
                            }
                            console.error(msg);
                            alert(msg);
                        }
                    } catch (err) {
                        this.checked = false;
                        this.disabled = false;
                        alert('Chyba siete alebo neočakávaná chyba. Skúste znova.');
                        console.error(err);
                    }
                });
            });
        });
    </script>
@endpush
