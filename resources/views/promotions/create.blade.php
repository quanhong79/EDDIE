@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Tạo khuyến mãi mới</h3>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('promotions.store') }}" method="POST" class="row g-3">
        @csrf

        <div class="col-md-6">
            <label class="form-label">Sản phẩm</label>
            <select name="product_id" class="form-select" required>
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ old('product_id')==$prod->id?'selected':'' }}>
                    {{ $prod->name }} (Giá: {{ number_format($prod->price,2) }})
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">% giảm</label>
            <input type="number" step="0.01" min="0" max="100" name="discount_percentage"
                value="{{ old('discount_percentage') }}" class="form-control" placeholder="VD: 10.5" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="datetime-local" name="start_date"
                value="{{ old('start_date') }}" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ngày kết thúc</label>
            <input type="datetime-local" name="end_date"
                value="{{ old('end_date') }}" class="form-control" required>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">Lưu</button>
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection