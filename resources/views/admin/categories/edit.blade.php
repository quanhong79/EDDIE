@extends('layouts.app')

@section('title','Edit Category')

@section('content')
<div class="container">
  <h2 class="mb-3">Edit: {{ $category->name }}</h2>

  <form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf @method('PUT')

    <div class="form-group mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="form-group mb-3">
      <label>Slug <small class="text-muted">(bỏ trống để tự tạo)</small></label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
      @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="form-group mb-4">
      <label>Parent Category</label>
      <select name="parent_id" class="form-control">
        <option value="">— Root (không có cha) —</option>
        @foreach($parents as $p)
          <option value="{{ $p->id }}"
            @selected( (string)old('parent_id', $category->parent_id) === (string)$p->id )>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
      @error('parent_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <button class="btn btn-primary">Save changes</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Cancel</a>
  </form>
</div>
@endsection
