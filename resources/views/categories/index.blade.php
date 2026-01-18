$extends('layouts.app')
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1">Kategórie</h1>
                <p class="text-muted small mb-0">Vytvorte si vlastné kategórie a používajte ich pri úlohách.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulár na vytvorenie kategórie --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST" class="row g-2 align-items-end">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">Názov</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Farba</label>
                        <input type="text" name="color" class="form-control" placeholder="#ff0000" value="{{ old('color') }}">
                        @error('color')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Pridať</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Zoznam kategórií --}}
        @if($categories->isEmpty())
            <div class="card p-4 text-center">
                <p class="mb-0">Zatiaľ nemáte žiadne kategórie.</p>
            </div>
        @else
            <div class="list-group">
                @foreach($categories as $category)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            @if($category->color)
                                <span style="width:14px;height:14px;border-radius:50%;background:{{ $category->color }};display:inline-block;"></span>
                            @endif
                            <span class="fw-semibold">{{ $category->name }}</span>
                            <span class="small text-muted">{{ $category->color }}</span>
                        </div>

                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                              onsubmit="return confirm('Naozaj chcete túto kategóriu zmazať?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Zmazať</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
