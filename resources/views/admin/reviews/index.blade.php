@extends('layouts.app')

@section('title','Quản lý bình luận')

@section('content')
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h1 class="mb-0 h4">Bình luận của khách hàng</h1>
    {{-- Bộ lọc --}}
    <form class="d-flex gap-2" method="GET">
      <input type="text" class="form-control form-control-sm" name="q" value="{{ request('q') }}"
             placeholder="Tìm theo nội dung / user / sản phẩm">
      <select name="status" class="form-select form-select-sm">
        @php $st = request('status'); @endphp
        <option value="">Tất cả trạng thái</option>
        <option value="pending"  {{ $st==='pending'  ? 'selected' : '' }}>Chờ duyệt</option>
        <option value="approved" {{ $st==='approved' ? 'selected' : '' }}>Đã duyệt</option>
        <option value="hidden"   {{ $st==='hidden'   ? 'selected' : '' }}>Đã ẩn</option>
      </select>
      <select name="min_rating" class="form-select form-select-sm" style="max-width:120px">
        @php $mr = (int) request('min_rating'); @endphp
        <option value="">Tối thiểu ★</option>
        @for($i=5;$i>=1;$i--)
          <option value="{{ $i }}" {{ $mr===$i ? 'selected' : '' }}>{{ $i }} ★</option>
        @endfor
      </select>
      <input type="text" class="form-control form-control-sm" name="product" value="{{ request('product') }}"
             placeholder="Tên sản phẩm">
      <button class="btn btn-sm btn-primary">
        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
      </button>
      @if(request()->query())
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-secondary">Xóa lọc</a>
      @endif
    </form>
  </div>

  {{-- Flash --}}
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  <div class="table-responsive">
    <table class="table align-middle table-hover">
      <thead class="table-light">
        <tr>
          <th style="width:70px">ID</th>
          <th style="min-width:220px">Sản phẩm</th>
          <th style="min-width:160px">User</th>
          <th>Nội dung</th>
          <th style="width:120px" class="text-center">Rating</th>
          <th style="width:140px" class="text-center">Trạng thái</th>
          <th style="width:220px" class="text-end">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $r)
          <tr>
            <td>#{{ $r->id }}</td>

            <td class="position-relative">
              @if($r->product)
                <a href="{{ route('product.show', $r->product->id) }}" target="_blank" rel="noopener"
                   class="fw-semibold text-decoration-none">
                  {{ $r->product->name }}
                </a>
              @else
                <span class="text-muted">Sản phẩm đã xoá</span>
              @endif
              <div class="small text-muted">
                {{ optional($r->created_at)->format('d/m/Y H:i') }}
              </div>
            </td>

            <td>
              {{ $r->user->name ?? 'Khách' }}
              <div class="small text-muted">{{ $r->user->email ?? '' }}</div>
            </td>

            <td>
              <div class="text-truncate" style="max-width: 520px" title="{{ $r->comment }}">
                {{ $r->comment }}
              </div>
            </td>

            <td class="text-center">
              <div class="text-warning">
                @for($i=1;$i<=5;$i++)
                  <i class="fa{{ $i <= (int)$r->rating ? 's' : 'r' }} fa-star"></i>
                @endfor
              </div>
              <div class="small text-muted">{{ (int)$r->rating }} / 5</div>
            </td>

            <td class="text-center">
              @php
                $badge = [
                  'pending'  => 'badge bg-warning text-dark',
                  'approved' => 'badge bg-success',
                  'hidden'   => 'badge bg-secondary',
                ];
                $label = [
                  'pending'  => 'Chờ duyệt',
                  'approved' => 'Đã duyệt',
                  'hidden'   => 'Đã ẩn',
                ];
                $stClass = $badge[$r->status] ?? 'badge bg-light text-dark';
              @endphp
              <span class="{{ $stClass }}">{{ $label[$r->status] ?? $r->status }}</span>
            </td>

            <td class="text-end">
              @if($r->status !== 'approved')
                <form action="{{ route('admin.reviews.approve', $r) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-success btn-sm rounded-pill">
                    <i class="fa-solid fa-check me-1"></i> Duyệt
                  </button>
                </form>
              @endif

              @if($r->status !== 'hidden')
                <form action="{{ route('admin.reviews.hide', $r) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Ẩn bình luận #{{ $r->id }}?');">
                  @csrf @method('PATCH')
                  <button class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fa-regular fa-eye-slash me-1"></i> Ẩn
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Không có bình luận phù hợp.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if(method_exists($reviews,'links'))
    <div class="d-flex justify-content-center mt-3">
      {{ $reviews->appends(request()->query())->links() }}
    </div>
  @endif
</div>
@endsection
