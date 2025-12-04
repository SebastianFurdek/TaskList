<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Vlastné CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar bg-dark text-white p-3" style="min-height: 100vh; width: 250px;">
        <h4 class="mb-4">Task Manager</h4>
        <hr class="border-light opacity-50 mb-3">

        <div class="nav flex-column">
            <!-- Vertical button group for sidebar actions -->
            <div class="d-grid gap-2">
                <a href="/dashboard" class="btn btn-outline-light text-start">Dashboard</a>

                <!-- Projekty button toggles the collapsible projects list -->
                <button class="btn btn-outline-light text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#projectsCollapse" aria-expanded="false" aria-controls="projectsCollapse">
                    Projekty
                    <span class="badge bg-light text-dark ms-2">3</span>
                </button>

                <!-- Collapsible static list of current projects (placeholder) -->
                <div class="collapse" id="projectsCollapse">
                    <div class="card card-body bg-transparent border-0 p-0 mt-2">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-dark text-white px-0">
                                <a href="/projects/1" class="text-white text-decoration-none">Projekt Alpha</a>
                            </li>
                            <li class="list-group-item bg-dark text-white px-0">
                                <a href="/projects/2" class="text-white text-decoration-none">Projekt Beta</a>
                            </li>
                            <li class="list-group-item bg-dark text-white px-0">
                                <a href="/projects/3" class="text-white text-decoration-none">Projekt Gamma</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="/tasks" class="btn btn-outline-light text-start">Úlohy</a>

                <a href="/logout" class="btn btn-danger text-start">Odhlásiť sa</a>
            </div>
        </div>
    </nav>

    <!-- Obsah -->
    <main class="p-4" style="width: 100%;">
        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
