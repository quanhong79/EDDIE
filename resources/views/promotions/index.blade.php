@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Danh sách khuyến mãi</h3>
        <a href="{{ route('promotions.create') }}" class="btn btn-primary">Tạo khuyến mãi mới</a>
    </div>

    {{-- Bộ lọc --}}
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $status==='all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="active" {{ $status==='active' ? 'selected' : '' }}>Đang hiệu lực</option>
                    <option value="expired" {{ $status==='expired' ? 'selected' : '' }}>Đã hết hạn</option>
                </select>
            </div>
        </div>
    </form>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
    @elseif (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($promotions->count() === 0)
    <div class="alert alert-info">Chưa có chương trình khuyến mãi.</div>
    @else
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>% giảm</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promotions as $p)
                <tr>
                    <td>{{ $p->product?->name ?? 'N/A' }}</td>
                    <td>{{ number_format($p->discount_percentage, 2) }}%</td>
                    <td>{{ $p->start_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $p->end_date->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                        {{-- Nút Xóa (tùy chọn) --}}
                        <form action="{{ route('promotions.destroy', $p) }}" method="POST" onsubmit="return confirm('Xóa khuyến mãi này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $promotions->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection