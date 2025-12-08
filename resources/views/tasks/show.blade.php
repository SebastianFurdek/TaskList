// filepath: resources/views/tasks/show.blade.php
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $task->title }}</h1>
        <p>{{ $task->description }}</p>
        <p>Termín: {{ $task->due_date ? $task->due_date->format('Y-m-d') : 'Neurčený' }}</p>
        <p>Status: {{ $task->completed ? 'Dokončené' : 'Prebieha' }}</p>

        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning">Upraviť</a>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Späť</a>
    </div>
@endsection

