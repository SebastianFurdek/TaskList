@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Administrácia používateľov</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Meno</th>
                        <th>Email</th>
                        <th>Rola</th>
                        <th>Vytvorený</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->role ?? 'user' }}</td>
                            <td>{{ $u->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                @if(auth()->user() && auth()->user()->getKey() !== $u->getKey())
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Zmazať</button>
                                    </form>
                                @else
                                    <span class="text-muted">(Vy)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

