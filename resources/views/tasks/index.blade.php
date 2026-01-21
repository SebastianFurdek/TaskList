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

                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm text-white">+ Nová úloha</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Project filter row --}}
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <label for="project-filter" class="form-label small text-muted">Filtrovať podľa projektu</label>
                <select id="project-filter" class="form-select form-select-sm">
                    <option value="">Všetky projekty</option>
                    @if(isset($projects) && $projects->count())
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        {{-- čast zoznamu úloh --}}
        <div id="tasks-list-container">
            @include('tasks._list', ['tasks' => $tasks])
        </div>

    </div>

    @push('scripts')
        <script>
            // Project filter AJAX handling
            document.addEventListener('DOMContentLoaded', function () {
                const filter = document.getElementById('project-filter');
                if (!filter) return;

                //listener na zmenu filtra
                filter.addEventListener('change', function () {
                    const pid = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('project_id', pid);

                    const container = document.getElementById('tasks-list-container');
                    if (!container) return;

                    container.classList.add('opacity-75');

                    fetch(url.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    }).then(function (resp) {
                        if (!resp.ok) throw resp;
                        return resp.json();
                    }).then(function (json) {
                        if (json && json.html !== undefined) {
                            container.innerHTML = json.html;
                        }
                        container.classList.remove('opacity-75');
                    }).catch(function (err) {
                        console.error('Filter error', err);
                        container.classList.remove('opacity-75');
                        alert('Chyba pri načítaní úloh pre tento projekt.');
                    });
                });
            });
        </script>
    @endpush
@endsection
