@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Category Details</h4>
            <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}">Back</a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label"><strong>Name:</strong></label>
                <div class="form-control-plaintext">{{ $category->name }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
