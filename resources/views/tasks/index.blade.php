@extends('layouts.app')

@section('content')
    <div class="container py-4">
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
            <div class="">
                @foreach($tasks as $task)
                    <div class="task-item d-flex align-items-center {{ $task->completed ? 'task-completed' : '' }}" data-task-id="{{ $task->id }}" data-complete-url="{{ route('tasks.complete', $task->id) }}">
                        {{-- checkbox (left) --}}
                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="me-2 complete-form">
                            @csrf
                            <input type="checkbox" class="form-check-input complete-checkbox" {{ $task->completed ? 'checked disabled' : '' }} title="Označiť ako dokončené">
                        </form>

                        {{-- title + single-line description (center), allow truncation --}}
                        <div class="flex-grow-1 ms-1 me-3" style="min-width:0;">
                            <div class="d-flex align-items-center flex-wrap">
                                <a href="{{ route('tasks.show', $task->id) }}" class="mb-0 text-decoration-none fw-semibold text-truncate {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}" style="max-width:100%;">{{ $task->title }}</a>
                                @if($task->project)
                                    <span class="badge bg-secondary ms-2 small">{{ $task->project->name }}</span>
                                @endif

                                {{-- category badges --}}
                                @if($task->categories && $task->categories->count())
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

                        {{-- due date + actions (right) --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="small text-muted text-end me-2" style="min-width:70px;">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') : '—' }}</div>

                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-light p-1 d-inline-flex align-items-center" title="Upraviť">
                                <!-- pencil SVG -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l2.0 2.0a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-4 1a.5.5 0 0 1-.63-.63l1-4a.5.5 0 0 1 .11-.168l10-10zM11.207 2L3 10.207V13h2.793L14 4.793 11.207 2z"/>
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = csrfMeta ? csrfMeta.getAttribute('content') : null;

            document.querySelectorAll('.complete-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', async function (e) {
                    if (this.disabled) return;

                    const form = this.closest('form.complete-form');
                    if (!form) return;

                    const taskItem = this.closest('.task-item');
                    const taskId = taskItem ? taskItem.getAttribute('data-task-id') : null;
                    // optimistically disable checkbox
                    this.disabled = true;

                    // helper to perform POST using XMLHttpRequest for better compatibility
                    const doPost = (url) => {
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
                            xhr.onload = function () {
                                // mimic fetch Response-like object minimally
                                resolve({ ok: xhr.status >= 200 && xhr.status < 400, status: xhr.status, text: () => Promise.resolve(xhr.responseText), url: url });
                            };
                            xhr.onerror = function (e) { reject(e); };
                            xhr.send(params.toString());
                        });
                    };

                    // helper to perform GET (used as a fallback)
                    const doGet = (url) => {
                        return fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });
                    };

                    try {
                        // construct absolute URL robustly (handles subfolders)
                        const dataUrl = taskItem ? taskItem.getAttribute('data-complete-url') : null;
                        let raw = dataUrl || form.action;
                        let url;
                        try {
                            url = new URL(raw, window.location.href).href;
                        } catch (err) {
                            url = raw; // fallback
                        }
                        console.debug('Submitting complete to', url, 'raw:', raw);
                        let res = await doPost(url);

                        // If 404, try fallback POST path then GET 'complete-now' endpoint
                        if (res.status === 404 && taskId) {
                            const altPath = '/tasks/' + taskId + '/complete';
                            let alt;
                            try { alt = new URL(altPath, window.location.href).href; } catch (err) { alt = altPath; }
                            console.debug('Received 404, retrying POST to', alt);
                            res = await doPost(alt);
                            if (res.status === 404) {
                                // try GET fallback
                                const getPath = '/tasks/' + taskId + '/complete-now';
                                let getUrl;
                                try { getUrl = new URL(getPath, window.location.href).href; } catch (err) { getUrl = getPath; }
                                console.debug('POST still 404, retrying GET to', getUrl);
                                const getRes = await doGet(getUrl).catch(() => null);
                                if (getRes && getRes.ok) {
                                    // treat as success
                                    res = { ok: true };
                                } else if (getRes) {
                                    res = { ok: false, status: getRes.status, text: () => getRes.text(), url: getUrl };
                                }
                            }
                        }

                        if (res.ok) {
                            if (taskItem) {
                                taskItem.style.transition = 'opacity .25s ease, transform .25s ease';
                                taskItem.style.opacity = '0';
                                taskItem.style.transform = 'translateY(-8px)';
                                setTimeout(function () { taskItem.remove(); }, 300);
                            } else {
                                window.location.reload();
                            }
                        } else if (res.status === 404) {
                            // treat 404 as 'already deleted or not accessible' -> remove from DOM quietly
                            if (taskItem) {
                                taskItem.style.transition = 'opacity .25s ease, transform .25s ease';
                                taskItem.style.opacity = '0';
                                taskItem.style.transform = 'translateY(-8px)';
                                setTimeout(function () { taskItem.remove(); }, 300);
                            } else {
                                window.location.reload();
                            }
                        } else {
                            // failure: revert checkbox and show detailed message
                            this.checked = false;
                            this.disabled = false;

                            let text = await res.text().catch(() => null);
                            let msg = `Pri označovaní úlohy nastala chyba: Server error ${res.status}`;
                            // handle auth / csrf issues specially
                            if (res.status === 401 || res.status === 419) {
                                msg += ' — nie ste prihlásený alebo vypršal CSRF token. Prosím prihláste sa/zopakujte akciu.';
                            }
                            // include attempted url and response snippet for debugging
                            const attemptedUrl = taskItem ? taskItem.getAttribute('data-complete-url') : (res.url || url);
                             msg += ` (url: ${attemptedUrl})`;
                             if (text) {
                                 try {
                                     const j = JSON.parse(text);
                                     if (j && j.message) msg += `: ${j.message}`;
                                     else msg += `: ${text.substring(0, 300)}`;
                                 } catch (_) {
                                     msg += `: ${text.substring(0, 300)}`;
                                 }
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
@endsection
