@extends('layouts.app')

@section('title', 'Products')

@section('content')
<h1 class="mb-3">Danh sách sản phẩm</h1>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  {{-- ĐÚNG: admin.products.create --}}
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
    + Thêm sản phẩm
  </a>

  {{-- Tìm kiếm + lọc danh mục --}}
  <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm theo tên…"
           value="{{ request('q') }}" style="min-width:220px;">

    <select name="category_id" class="form-control form-control-sm" style="min-width:220px;">
      <option value="">— Tất cả danh mục —</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>
          {{ $cat->name }}
        </option>
      @endforeach
    </select>

    <button class="btn btn-outline-secondary btn-sm">Lọc</button>

    @if(request()->hasAny(['q','category_id']))
      {{-- ĐÚNG: admin.products.index --}}
      <a href="{{ route('admin.products.index') }}" class="btn btn-link btn-sm">Xoá lọc</a>
    @endif
  </form>
</div>

<div class="table-responsive">
  <table class="table table-bordered align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:70px;">ID</th>
        <th style="width:84px;">Ảnh</th>
        <th>Tên sản phẩm</th>
        <th style="width:160px;">Sizes</th>
        <th style="width:160px;">Màu sắc</th>
        <th style="width:120px;">Giá</th>
        <th style="width:90px;">SL</th>
        <th style="width:120px;">Trạng thái</th>
        <th style="width:160px;">Danh mục</th>
        <th style="width:120px;">Gallery</th>
        <th style="width:220px;">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($products as $product)
        @php
          // Ép về mảng an toàn nếu DB lưu JSON string
          $sizes  = is_string($product->sizes)  ? (json_decode($product->sizes, true)  ?: []) : ((array)($product->sizes ?? []));
          $colors = is_string($product->colors) ? (json_decode($product->colors, true) ?: []) : ((array)($product->colors ?? []));
          $inStock = (int)($product->quantity ?? 0) > 0;

          // Ưu tiên accessor image_url nếu có
          $imageUrl = (method_exists($product, 'getImageUrlAttribute') && !empty($product->image_url))
                      ? $product->image_url
                      : ($product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png'));
        @endphp
        <tr>
          <td>{{ $product->id }}</td>
          <td>
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" style="width:56px;height:56px;object-fit:cover;border-radius:6px;">
          </td>
          <td class="fw-semibold">
            <div class="text-truncate" style="max-width:260px;">{{ $product->name }}</div>
            <div class="small text-muted text-truncate" style="max-width:260px;">
              {{ $product->description }}
            </div>
          </td>

          {{-- Sizes --}}
          <td>
            @if(count($sizes))
              <div class="d-flex flex-wrap" style="gap:.25rem;">
                @foreach($sizes as $sz)
                  <span class="badge bg-dark text-white">{{ $sz }}</span>
                @endforeach
              </div>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          {{-- Colors --}}
          <td>
            @if(count($colors))
              <div class="d-flex flex-wrap" style="gap:.25rem;">
                @foreach($colors as $c)
                  <span class="badge bg-secondary">{{ $c }}</span>
                @endforeach
              </div>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          {{-- Giá --}}
          <td class="text-nowrap">{{ number_format((float)$product->price, 0, ',', '.') }} VNĐ</td>

          {{-- Số lượng --}}
          <td><span class="badge bg-secondary">{{ (int)$product->quantity }}</span></td>

          {{-- Trạng thái --}}
          <td>
            @if($inStock)
              <span class="badge bg-success">Còn hàng</span>
            @else
              <span class="badge bg-danger">Hết hàng</span>
            @endif
          </td>

          {{-- Danh mục --}}
          <td>{{ optional($product->category)->name ?? 'N/A' }}</td>

          {{-- Gallery (cần withCount('images') từ Controller) --}}
          <td><span class="badge bg-info text-dark">{{ $product->images_count ?? 0 }}</span> ảnh</td>

          {{-- Thao tác --}}
          <td>
            <div class="d-flex flex-wrap" style="gap:.5rem;">
              {{-- ĐÚNG: admin.products.* --}}
              <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm">Chi tiết</a>
              <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
              <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                    onsubmit="return confirm('Bạn chắc chắn muốn xoá?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Xoá</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="11" class="text-center text-muted">Chưa có sản phẩm.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Phân trang --}}
@if(method_exists($products, 'links'))
  <div class="d-flex justify-content-center mt-3">
    {{ $products->withQueryString()->links() }}
  </div>
@endif
@endsection
