@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Create New Category</h4>
            <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
