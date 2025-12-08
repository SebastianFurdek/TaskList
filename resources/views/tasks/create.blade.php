@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Nová úloha</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            @include('tasks._form')
        </form>
    </div>
@endsection
