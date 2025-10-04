@extends('layouts.app')

@section('title', $category->parent ? $category->parent->name : $category->name)

@section('content')
@php
  $root = $category->parent ?: $category;
  $subs = $root->children;
@endphp

<div class="mb-4">
  <h1 class="h4 mb-3">{{ $root->name }}</h1>
  <ul class="nav nav-pills flex-wrap" style="gap:.35rem;">
    <li class="nav-item">
      <a class="nav-link {{ $category->id === $root->id ? 'active' : '' }}"
         href="{{ route('category.show', $root->slug) }}">Tất cả</a>
    </li>
    @foreach($subs as $c)
      <li class="nav-item">
        <a class="nav-link {{ $category->id === $c->id ? 'active' : '' }}"
           href="{{ route('category.show', $c->slug) }}">{{ $c->name }}</a>
      </li>
    @endforeach
  </ul>
</div>

@if($products->count())
  <div class="row">
    @foreach($products as $product)
      @php
        $imageUrl = $product->image ? asset('storage/'.$product->image) : asset('img/placeholder.png');
        $inStock  = (int)($product->quantity ?? 0) > 0;
      @endphp
      <div class="col-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 border-0 shadow-sm">
          <a href="{{ route('product.show', $product->id) }}">
            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $product->name }}"
                 style="width:100%;height:220px;object-fit:cover;">
          </a>
          <div class="card-body d-flex flex-column text-center">
            <h6 class="mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
            <div class="small text-muted mb-2">{{ optional($product->category)->name }}</div>
            <div class="fw-bold text-success mb-2">{{ number_format($product->price) }} VNĐ</div>
            <div class="mb-3">
              @if($inStock) <span class="badge bg-success">Còn hàng</span>
              @else <span class="badge bg-danger">Hết hàng</span> @endif
            </div>
            <div class="mt-auto">
              @auth
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-dark btn-sm me-1" {{ $inStock ? '' : 'disabled' }}>
                    <i class="fas fa-cart-plus me-1"></i> Thêm
                  </button>
                </form>
              @else
                <a href="{{ route('login') }}" class="btn btn-dark btn-sm me-1">
                  <i class="fas fa-cart-plus me-1"></i> Thêm
                </a>
              @endauth
              <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-secondary btn-sm">Chi tiết</a>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="d-flex justify-content-center">
    {{ $products->withQueryString()->links() }}
  </div>
@else
  <div class="alert alert-light border text-center">Chưa có sản phẩm trong danh mục này.</div>
@endif
@endsection
