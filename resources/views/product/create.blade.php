@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
    <h1>Add New Product</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('product.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="">-- Choose Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Create</button>
        <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
