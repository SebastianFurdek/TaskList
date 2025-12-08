// filepath: resources/views/tasks/edit.blade.php
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Upraviť úlohu</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('tasks._form')
        </form>
    </div>
@endsection

