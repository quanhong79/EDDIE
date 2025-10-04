@extends('layouts.app')

@section('title','Trang chủ')

@section('content')
@php $brandText = 'Eddie'; @endphp

{{-- ===================== HERO / BANNER TRÊN CÙNG ===================== --}}
<div class="container-fluid px-0">
  <div id="homeHero" class="carousel slide hero" data-ride="carousel" data-interval="5000">
    <ol class="carousel-indicators">
      <li data-target="#homeHero" data-slide-to="0" class="active"></li>
      <li data-target="#homeHero" data-slide-to="1"></li>
      <li data-target="#homeHero" data-slide-to="2"></li>
    </ol>

    <div class="carousel-inner">
      {{-- Slide 1 --}}
      <div class="carousel-item active">
        <div class="hero-slide" style="background-image:url('{{ asset('img/hero1.jpg') }}');">
          <div class="hero-caption container">
            <h1 class="display-4 font-weight-bold text-white mb-2">New Season Arrivals</h1>
          </div>
        </div>
      </div>
      {{-- Slide 2 --}}
      <div class="carousel-item">
        <div class="hero-slide" style="background-image:url('{{ asset('img/hero2.jpg') }}');">
          <div class="hero-caption container">
            <h1 class="display-4 font-weight-bold text-white mb-2">Make U Confident</h1>
          </div>
        </div>
      </div>
      {{-- Slide 3 --}}
      <div class="carousel-item">
        <div class="hero-slide" style="background-image:url('{{ asset('img/hero3.jpg') }}');">
          <div class="hero-caption container">
            <h1 class="display-4 font-weight-bold text-white mb-2">Look Better</h1>
          </div>
        </div>
      </div>
    </div>

    <a class="carousel-control-prev" href="#homeHero" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#homeHero" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
</div>
{{-- ===================== /HERO ===================== --}}

<div class="container py-4">
  <div class="row gy-5">
    @foreach($products as $product)
      @php
        $imageUrl = $product->image ? asset('storage/'.$product->image) : asset('img/placeholder.png');
        $inStock  = (int)($product->quantity ?? 0) > 0;
      @endphp

      <div class="col-6 col-md-4 col-lg-3">
        <div class="atino-card">
          {{-- Brand --}}
          <div class="atino-brand">{{ $brandText }}</div>

          {{-- Ảnh chính --}}
          <a href="{{ route('product.show',$product->id) }}" class="atino-img-wrap">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="atino-img">
          </a>

          {{-- Thumbnails: dùng quan hệ images --}}
          @if($product->images && $product->images->count())
            <div class="atino-variants">
              @foreach($product->images->take(5) as $img)
                <img src="{{ $img->url ?? asset('storage/'.$img->path) }}" class="atino-variant" alt="">
              @endforeach
            </div>
          @endif

          {{-- Tên + Giá --}}
          <div class="atino-name text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
          <div class="atino-price">{{ number_format($product->price) }}₫</div>

          {{-- Hành động --}}
          <div class="atino-actions mt-2">
            @auth
              <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm px-3 me-1"
                        {{ $inStock ? '' : 'disabled' }}
                        title="{{ $inStock ? 'Thêm vào giỏ' : 'Sản phẩm đã hết hàng' }}">
                  <i class="fas fa-cart-plus me-1"></i> Thêm
                </button>
              </form>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3 me-1">
                <i class="fas fa-cart-plus me-1"></i> Thêm
              </a>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @endauth
          </div>
        </div>
      </div>
    @endforeach
  </div>
  {{-- ===================== BÁN CHẠY NHẤT ===================== --}}
@php $brandText = $brandText ?? 'Eddie'; @endphp
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h4 mb-0">Bán chạy nhất</h2>
    <a href="{{ route('product.index') }}" class="text-decoration-none">Xem thêm</a>
  </div>

  <div class="row gy-5">
    @forelse($topSellers as $product)
      @php
        $imageUrl = $product->image ? asset('storage/'.$product->image) : asset('img/placeholder.png');
        $inStock  = (int)($product->quantity ?? 0) > 0;
      @endphp

      <div class="col-6 col-md-4 col-lg-3">
        <div class="atino-card">
          <div class="atino-brand">{{ $brandText }}</div>

          <a href="{{ route('product.show',$product->id) }}" class="atino-img-wrap">
            <img class="atino-img" src="{{ $imageUrl }}" alt="{{ $product->name }}">
          </a>

          <div class="atino-name text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
          <div class="atino-price">{{ number_format($product->price) }}₫</div>

          <div class="atino-actions mt-2">
            @auth
              <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm px-3 me-1" {{ $inStock ? '' : 'disabled' }}>
                  <i class="fas fa-cart-plus me-1"></i> Thêm
                </button>
              </form>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3 me-1">
                <i class="fas fa-cart-plus me-1"></i> Thêm
              </a>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @endauth
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p class="text-muted">Chưa có dữ liệu đơn hàng để xếp hạng.</p></div>
    @endforelse
  </div>
</div>

{{-- ===================== KHÁM PHÁ THÊM ===================== --}}
<div class="container py-2">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h4 mb-0">Khám phá thêm</h2>
    <a href="{{ route('product.index') }}" class="text-decoration-none">Xem thêm</a>
  </div>

  <div class="row gy-5">
    @foreach($otherProducts as $product)
      @php
        $imageUrl = $product->image ? asset('storage/'.$product->image) : asset('img/placeholder.png');
        $inStock  = (int)($product->quantity ?? 0) > 0;
      @endphp

      <div class="col-6 col-md-4 col-lg-3">
        <div class="atino-card">
          <div class="atino-brand">{{ $brandText }}</div>

          <a href="{{ route('product.show',$product->id) }}" class="atino-img-wrap">
            <img class="atino-img" src="{{ $imageUrl }}" alt="{{ $product->name }}">
          </a>

          <div class="atino-name text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
          <div class="atino-price">{{ number_format($product->price) }}₫</div>

          <div class="atino-actions mt-2">
            @auth
              <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm px-3 me-1" {{ $inStock ? '' : 'disabled' }}>
                  <i class="fas fa-cart-plus me-1"></i> Thêm
                </button>
              </form>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @else
              <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3 me-1">
                <i class="fas fa-cart-plus me-1"></i> Thêm
              </a>
              <a href="{{ route('product.show',$product->id) }}" class="btn btn-outline-secondary btn-sm px-3">Chi tiết</a>
            @endauth
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

</section>
  {{-- Phân trang (giữ query nếu có) --}}
  @if(method_exists($products,'links'))
    <div class="d-flex justify-content-center mt-4">
      {{ $products->appends(request()->query())->links() }}
    </div>
  @endif
</div>

{{-- Styles cho HERO + thẻ sản phẩm --}}
<style>
  /* HERO */
  .hero .carousel-item{ height: clamp(560px, 85vh, 980px); }
  @media (max-width: 576px){
    .hero .carousel-item{ height: 70vh; min-height: 520px; }
  }
  .hero-slide{
    position: relative; height: 100%;
    background-size: cover; background-position: center;
  }
  .hero-slide::before{
    content:""; position:absolute; inset:0;
    background: linear-gradient(90deg, rgba(0,0,0,.20), rgba(0,0,0,.05));
    pointer-events:none;
  }
  .hero-caption{
    position:absolute; top:50%; left:0; right:0; transform:translateY(-50%);
  }
  .hero-caption .display-4{ text-shadow: 0 4px 18px rgba(0,0,0,.45); }

  /* PRODUCT CARDS (Atino-style) */
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
