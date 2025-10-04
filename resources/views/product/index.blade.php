@extends('layouts.app')

@section('title', isset($category) ? $category->name : 'Sản phẩm')

@section('content')
@php $brandText = 'Eddie'; @endphp

<div class="container py-3">
  <h1 class="mb-3">
    {{ isset($category) ? $category->name : 'Tất cả sản phẩm' }}
  </h1>

  {{-- Bộ lọc --}}
 @includeWhen(!empty($showFilter), 'partials.product-filter', ['category' => $category ?? null])

  {{-- Lưới sản phẩm --}}
  <div class="row gy-5">
  @forelse($products as $product)
    @php
      $imageUrl = method_exists($product, 'getImageUrlAttribute')
                  ? $product->image_url
                  : ($product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png'));
      $inStock  = (int)($product->quantity ?? 0) > 0;
      $hasColors = is_array($product->colors ?? null) && count($product->colors ?? []) > 0;
      $hasSizes  = is_array($product->sizes  ?? null) && count($product->sizes  ?? []) > 0;
      $needsOptions = $hasColors || $hasSizes;
    @endphp

    <div class="col-6 col-md-4 col-lg-3">
      <div class="atino-card position-relative">
        {{-- Brand --}}
        <div class="atino-brand">{{ $brandText }}</div>

        {{-- Ảnh --}}
        <a href="{{ route('product.show', $product->id) }}" class="atino-img-wrap">
          <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="atino-img" loading="lazy">
        </a>

        {{-- Badge tồn kho --}}
        <div class="position-absolute top-0 end-0 m-2">
          @if($inStock)
            <span class="badge bg-success rounded-pill">Còn</span>
          @else
            <span class="badge bg-danger rounded-pill">Hết</span>
          @endif
        </div>

        {{-- Thumbnail variants (nếu có) --}}
        @if($product->images && $product->images->count())
          <div class="atino-variants">
            @foreach($product->images->take(5) as $img)
              <img src="{{ asset('storage/'.$img->path) }}" class="atino-variant" alt="" loading="lazy">
            @endforeach
          </div>
        @endif

        {{-- Tên + giá --}}
        <div class="atino-name text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
        <div class="atino-price">{{ number_format((float)$product->price) }}₫</div>

        {{-- Hành động --}}
        <div class="atino-actions mt-2">
          @if($needsOptions)
            {{-- Cần chọn biến thể: đưa sang trang chi tiết --}}
            <a href="{{ route('product.show', $product->id) }}"
               class="btn btn-dark btn-sm px-3 me-1 {{ $inStock ? '' : 'disabled' }}"
               title="{{ $inStock ? 'Chọn tuỳ chọn' : 'Sản phẩm đã hết hàng' }}">
              <i class="fas fa-sliders-h me-1"></i> Chọn tuỳ chọn
            </a>
          @else
            {{-- Không có biến thể: cho phép thêm trực tiếp --}}
            @auth
              <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-dark btn-sm px-3 me-1"
                        {{ $inStock ? '' : 'disabled' }}
                        title="{{ $inStock ? 'Thêm vào giỏ' : 'Sản phẩm đã hết hàng' }}">
                  <i class="fas fa-cart-plus me-1"></i> Thêm
                </button>
              </form>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3 me-1">
                <i class="fas fa-cart-plus me-1"></i> Thêm
              </a>
            @endauth
          @endif

          <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-secondary btn-sm px-3">
            Chi tiết
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center text-muted py-5">Không tìm thấy sản phẩm phù hợp.</div>
  @endforelse
</div>

  {{-- Phân trang --}}
  @if(method_exists($products,'links'))
    <div class="d-flex justify-content-center mt-4">
      {{ $products->appends(request()->query())->links() }}
    </div>
  @endif
</div>

{{-- CSS --}}
<style>
  .atino-card{ text-align:center; background:#fff; border:0; }
  .atino-brand{ font-family:"Times New Roman",serif; letter-spacing:.08em; color:#222; font-size:20px; margin-bottom:10px; }
  .atino-img-wrap{ display:block; width:100%; aspect-ratio:1/1; background:#f5f5f7; border-radius:6px; overflow:hidden; }
  .atino-img{ width:100%; height:100%; object-fit:cover; transition:transform .25s ease; }
  .atino-img-wrap:hover .atino-img{ transform:scale(1.03); }
  .atino-variants{ display:flex; justify-content:center; gap:8px; margin:10px 0 6px; }
  .atino-variant{ width:22px; height:22px; border-radius:50%; object-fit:cover; border:1px solid #e5e7eb; }
  .atino-name{ margin-top:6px; color:#1f2937; font-size:15px; line-height:1.35; min-height:1.35em; }
  .atino-price{ margin-top:6px; font-weight:700; color:#111827; letter-spacing:.02em; }
  .atino-actions .btn{ border-radius:9999px; font-weight:600; }
  .atino-actions .btn i{ font-size:.9rem; }
  .gy-5 > [class^="col-"]{ margin-bottom:28px; }
</style>
@endsection
