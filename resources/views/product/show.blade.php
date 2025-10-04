{{-- resources/views/product/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
  $primaryUrl = $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png');
  $gallery    = $product->images ?? collect();
  $slides     = $gallery->take(6)->pluck('path')->map(fn($p) => asset('storage/'.$p))->all();
  if (!count($slides)) $slides = [$primaryUrl];

  $inStock   = (int)($product->quantity ?? 0) > 0;
  $cat       = $product->category;
  $colors    = (array)($product->colors ?? []);
  $sizeMode  = $product->size_mode ?? 'none';
  $sizes     = (array)($product->sizes ?? []);

  $avgRating   = round($product->approvedReviews()->avg('rating') ?? 0, 1);
  $avgScore    = number_format((float)$avgRating, 1);
  $totalReview = $product->approvedReviews()->count();
@endphp

<div class="container py-4 product-show">
  {{-- Breadcrumb --}}
  <nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
      @if($cat)
        <li class="breadcrumb-item"><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
    </ol>
  </nav>

  <div class="row g-4">
    {{-- LEFT: Gallery --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-3">
        <div class="ratio ratio-1x1 rounded-3 bg-light overflow-hidden">
          <img id="mainImage" src="{{ $slides[0] }}" class="w-100 h-100 object-cover" alt="{{ $product->name }}">
        </div>

        @if(count($slides) > 1)
          <div class="thumbs mt-3 d-flex flex-wrap gap-2">
            @foreach($slides as $i => $u)
              <button type="button" class="thumb {{ $i===0 ? 'active' : '' }}" onclick="swapImage('{{ $u }}', this)">
                <img src="{{ $u }}" alt="Ảnh {{ $i+1 }}">
              </button>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- RIGHT: Info --}}
    <div class="col-lg-6">
      <div class="d-flex align-items-start justify-content-between gap-3">
        <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>
        <div class="text-nowrap">
          {!! $inStock ? '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Còn hàng</span>'
                       : '<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Hết hàng</span>' !!}
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 text-muted mb-2">
        @if($cat)
          <a class="badge rounded-pill bg-info-subtle text-info-emphasis" href="{{ route('category.show', $cat->slug) }}">
            {{ $cat->name }}
          </a>
        @endif

        <span class="vr"></span>
        <span class="rating">
          @for($i=1;$i<=5;$i++)
            <i class="fa{{ $i <= round($avgRating) ? 's' : 'r' }} fa-star"></i>
          @endfor
          <small class="ms-1">{{ $avgScore }}/5 ({{ $totalReview }})</small>
        </span>
      </div>

      <div class="display-price mb-3">
        <span class="price">{{ number_format((float)$product->price, 0, ',', '.') }}₫</span>
      </div>

      {{-- Variants --}}
      @if(!empty($colors))
        <div class="mb-3">
          <label for="colorSelect" class="form-label fw-semibold">Màu sắc</label>
          <select id="colorSelect" class="form-select w-auto" {{ $inStock ? 'required' : 'disabled' }}>
            <option value="">-- Chọn màu --</option>
            @foreach($colors as $c)
              <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
          </select>
          <div id="colorError" class="text-danger small mt-1 d-none">Vui lòng chọn màu</div>
        </div>
      @endif

      @if($sizeMode !== 'none' && !empty($sizes))
        <div class="mb-3">
          <label class="form-label fw-semibold">Kích cỡ</label>
          <div id="sizeGroup" class="d-flex flex-wrap gap-2">
            @foreach($sizes as $sz)
              <input type="radio" class="btn-check" name="size_choice" id="size_{{ $sz }}" value="{{ $sz }}" {{ $inStock ? '' : 'disabled' }}>
              <label class="btn btn-outline-dark btn-sm rounded-pill" for="size_{{ $sz }}">{{ $sz }}</label>
            @endforeach
          </div>
          <div id="sizeError" class="text-danger small mt-1 d-none">Vui lòng chọn size</div>
        </div>
      @endif

      {{-- Quantity --}}
      <div class="mb-3">
        <label for="qty" class="form-label">Số lượng</label>
        <input type="number" id="qty" class="form-control w-auto" min="1" value="1" {{ $inStock ? '' : 'disabled' }}>
      </div>

      {{-- Actions --}}
      <div class="d-flex flex-wrap gap-2 mb-4 sticky-actions">
        <form action="{{ route('cart.add', $product->id) }}" method="POST"
              class="js-add-to-cart" onsubmit="return injectAndValidate(this)">
          @csrf
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="color">
          <input type="hidden" name="size">
          <button type="submit" class="btn btn-success btn-lg px-4 rounded-pill" {{ $inStock ? '' : 'disabled' }}>
            <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ
          </button>
        </form>

        <form action="{{ route('cart.add', $product->id) }}" method="POST"
              class="js-add-to-cart" data-redirect="cart" onsubmit="return injectAndValidate(this)">
          @csrf
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="color">
          <input type="hidden" name="size">
          <input type="hidden" name="buy_now" value="1">
          <button type="submit" class="btn btn-primary btn-lg px-4 rounded-pill" {{ $inStock ? '' : 'disabled' }}>
            Mua ngay
          </button>
        </form>
      </div>

      {{-- Tabs: CHỈ mô tả ở "Mô tả"; CHỈ review ở "Đánh giá" --}}
      <ul class="nav nav-tabs mt-4" id="prodTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">
            Mô tả
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
            Thông tin thêm
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="rvw-tab" data-bs-toggle="tab" data-bs-target="#rvw" type="button" role="tab">
            Đánh giá ({{ $totalReview }})
          </button>
        </li>
      </ul>

      <div class="tab-content border rounded-bottom p-3 bg-white shadow-sm" id="prodTabsContent">
        {{-- TAB MÔ TẢ: chỉ mô tả --}}
        <div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">
          <div class="text-body">{!! nl2br(e($product->description ?? 'Chưa có mô tả.')) !!}</div>
        </div>

        {{-- TAB THÔNG TIN THÊM (tuỳ chọn) --}}
        <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
          <ul class="list-unstyled mb-0">
            <li class="d-flex justify-content-between py-1 border-bottom">
              <span>Danh mục</span><strong>{{ $cat->name ?? '—' }}</strong>
            </li>
            <li class="d-flex justify-content-between py-1 border-bottom">
              <span>Màu</span><strong>{{ !empty($colors) ? implode(', ', $colors) : '—' }}</strong>
            </li>
            <li class="d-flex justify-content-between py-1">
              <span>Kích cỡ</span><strong>{{ !empty($sizes) ? implode(', ', $sizes) : '—' }}</strong>
            </li>
          </ul>
        </div>

        {{-- TAB ĐÁNH GIÁ: chỉ review (form + list) --}}
        <div class="tab-pane fade" id="rvw" role="tabpanel" aria-labelledby="rvw-tab">
          {{-- Form đánh giá (mỗi user 1 review/sản phẩm) --}}
          @auth
            @php $myReview = $product->reviews()->where('user_id', auth()->id())->first(); @endphp
            @if(!$myReview)
              <form action="{{ route('reviews.store', $product) }}" method="POST" class="mb-3">
                @csrf
                <div class="row g-2 align-items-end">
                  <div class="col-12 col-sm-3">
                    <label class="form-label mb-1">Đánh giá</label>
                    <select name="rating" class="form-select" required>
                      <option value="">-- Chọn --</option>
                      @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }} ★</option> @endfor
                    </select>
                  </div>
                  <div class="col-12 col-sm-7">
                    <label class="form-label mb-1">Nhận xét</label>
                    <input type="text" name="comment" class="form-control" maxlength="1000"
                           placeholder="Chia sẻ cảm nhận của bạn..." required>
                  </div>
                  <div class="col-12 col-sm-2">
                    <button class="btn btn-primary w-100">Gửi</button>
                  </div>
                </div>
              </form>
            @else
              <div class="alert alert-success py-2">✅ Bạn đã đánh giá sản phẩm này.</div>
            @endif
          @else
            <div class="alert alert-info">Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</div>
          @endauth

          {{-- Danh sách bình luận đã duyệt --}}
          @php
            $reviews = $reviews ?? $product->approvedReviews()->with('user')->latest()->paginate(8);
          @endphp

          @forelse($reviews as $rv)
            <div class="list-group-item px-0 py-3 border-bottom">
              <div class="d-flex justify-content-between">
                <strong>{{ $rv->user->name ?? 'Khách' }}</strong>
                <span class="text-muted small">{{ optional($rv->created_at)->format('d/m/Y H:i') }}</span>
              </div>
              <div class="small text-warning mb-1">
                @for($i=1;$i<=5;$i++)
                  <i class="fa{{ $i <= (int)$rv->rating ? 's' : 'r' }} fa-star"></i>
                @endfor
              </div>
              <div>{{ $rv->comment }}</div>
            </div>
          @empty
            <p class="text-muted mb-0">Chưa có bình luận nào.</p>
          @endforelse

          @if(method_exists($reviews, 'links'))
            <div class="d-flex justify-content-center mt-3">
              {{ $reviews->appends(request()->query())->links() }}
            </div>
          @endif
        </div>
      </div>
      {{-- /tab-content --}}
    </div>
  </div>
</div>

{{-- Styles --}}
<style>
  .product-show .object-cover{object-fit:cover;}
  .product-show .thumbs .thumb{
    width:74px;height:74px;border:2px solid #eee;border-radius:10px;overflow:hidden;padding:0;background:#fff;cursor:pointer;
  }
  .product-show .thumbs .thumb.active{border-color:#111;}
  .product-show .thumbs .thumb img{width:100%;height:100%;object-fit:cover;display:block;}
  .product-show .display-price .price{font-size:1.75rem;font-weight:800;color:#0f5132;}
  .product-show .sticky-actions .btn{box-shadow:0 6px 16px rgba(0,0,0,.08);}
  .product-show .rating .fa-star{font-size:.95rem;color:#f6c343;}
  .product-show .nav-tabs .nav-link{font-weight:600}
  .product-show .nav-tabs + .tab-content{border-top:0;border-radius:0 0 .5rem .5rem}
</style>

{{-- Scripts --}}
<script>
function swapImage(url, el) {
  const img = document.getElementById('mainImage');
  img.src = url;
  document.querySelectorAll('.thumbs .thumb').forEach(t => t.classList.remove('active'));
  if (el) el.classList.add('active');
}

function injectAndValidate(form) {
  // quantity
  const qtyInput = document.getElementById('qty');
  if (qtyInput) {
    form.querySelector('input[name="quantity"]').value = Math.max(1, parseInt(qtyInput.value || 1, 10));
  }
  // color (nếu có)
  const colorSelect = document.getElementById('colorSelect');
  if (colorSelect) {
    if (!colorSelect.value) {
      document.getElementById('colorError')?.classList.remove('d-none');
      return false;
    }
    form.querySelector('input[name="color"]').value = colorSelect.value;
  }
  // size (nếu có)
  const sizeGroup = document.getElementById('sizeGroup');
  if (sizeGroup) {
    const picked = sizeGroup.querySelector('input:checked');
    if (!picked) {
      document.getElementById('sizeError')?.classList.remove('d-none');
      return false;
    }
    form.querySelector('input[name="size"]').value = picked.value;
  }
  return true;
}
</script>
@endsection
