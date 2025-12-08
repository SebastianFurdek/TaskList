@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Moje úlohy</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">+ Nová úloha</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Termín</th>
                <th>Akcie</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td><a href="{{ route('tasks.show', $task->id) }}">{{ $task->title }}</a></td>
                    <td>{{ $task->due_date }}</td>
                    <td>
                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm">Zobraziť</a>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Upraviť</a>

                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Zmazať</button>
                        </form>

                        <form action="{{ route('tasks.complete', $task->id) }}" method="POST" style="display: inline; margin-left:6px;">
                            @csrf
                            <button class="btn btn-success btn-sm">Označiť ako dokončené</button>
                        </form>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
