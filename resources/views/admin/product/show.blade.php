@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
  $imageUrl    = method_exists($product, 'getImageUrlAttribute')
                  ? $product->image_url
                  : ($product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png'));
  $inStock     = (int) ($product->quantity ?? 0) > 0;
  $category    = optional($product->category)->name ?? 'Chưa phân loại';

  // Tính nhanh điểm + tổng số đánh giá duyệt
  $avgRating   = round($product->approvedReviews()->avg('rating') ?? 0, 1);
  $totalReview = $product->approvedReviews()->count();

  $hasColors = is_array($product->colors ?? null) && count($product->colors ?? []) > 0;
  $hasSizes  = is_array($product->sizes  ?? null) && count($product->sizes  ?? []) > 0;
@endphp

<div class="container py-4">
  <div class="row g-4">
    {{-- LEFT: ẢNH --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
             class="w-100 d-block object-cover" style="aspect-ratio: 1 / 1; object-fit: cover;">
      </div>
    </div>

    {{-- RIGHT: THÔNG TIN CƠ BẢN + MUA --}}
    <div class="col-lg-6">
      <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>

      <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
        <span class="badge bg-info text-dark">Danh mục: {{ $category }}</span>
        {!! $inStock
          ? '<span class="badge bg-success">Còn hàng</span>'
          : '<span class="badge bg-danger">Hết hàng</span>' !!}
      </div>

      {{-- Giá --}}
      <div class="mb-3">
        <div class="fs-4 fw-bold text-success">💰 {{ number_format((float)$product->price) }} VNĐ</div>
      </div>

      {{-- Màu --}}
      @if($hasColors)
        <div class="mb-3">
          <label class="form-label">Màu sắc</label>
          <div class="d-flex flex-wrap gap-2" id="color-group" role="group" aria-label="Chọn màu">
            @foreach($product->colors as $c)
              <button type="button" class="btn btn-outline-secondary btn-sm attr-pill"
                      data-attr="color" data-value="{{ $c }}" aria-pressed="false">
                {{ $c }}
              </button>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Size --}}
      @if($hasSizes)
        <div class="mb-3">
          <label class="form-label">Kích cỡ</label>
          <div class="d-flex flex-wrap gap-2" id="size-group" role="group" aria-label="Chọn size">
            @foreach($product->sizes as $s)
              <button type="button" class="btn btn-outline-secondary btn-sm attr-pill"
                      data-attr="size" data-value="{{ $s }}" aria-pressed="false">
                {{ $s }}
              </button>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Số lượng --}}
      <div class="mb-3">
        <label class="form-label">Số lượng</label>
        <input type="number" id="qty" class="form-control w-auto" min="1" value="1" {{ $inStock ? '' : 'disabled' }}>
      </div>

      {{-- Hành động --}}
      <div class="d-flex align-items-center gap-2 mb-4">
        <form action="{{ route('cart.add', $product->id) }}" method="POST"
              onsubmit="return injectQtyAndOptions(this, {{ $hasColors ? 'true' : 'false' }}, {{ $hasSizes ? 'true' : 'false' }})">
          @csrf
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="color" value="">
          <input type="hidden" name="size" value="">
          <button type="submit" class="btn btn-success px-4" @disabled(!$inStock)>
            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
          </button>
        </form>

        <form action="{{ route('cart.add', $product->id) }}" method="POST"
              onsubmit="return injectQtyAndOptions(this, {{ $hasColors ? 'true' : 'false' }}, {{ $hasSizes ? 'true' : 'false' }})">
          @csrf
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="buy_now" value="1">
          <input type="hidden" name="color" value="">
          <input type="hidden" name="size" value="">
          <button type="submit" class="btn btn-primary px-4" @disabled(!$inStock)>
            ⚡ Mua ngay
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ===================== TABS: MÔ TẢ / ĐÁNH GIÁ ===================== --}}
  <ul class="nav nav-tabs mt-4" id="prodTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="desc-tab" data-bs-toggle="tab" href="#desc" role="tab">Mô tả</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="rvw-tab" data-bs-toggle="tab" href="#rvw" role="tab">Đánh giá ({{ $totalReview }})</a>
    </li>
  </ul>

  <div class="tab-content border rounded-bottom p-3 bg-white shadow-sm" id="prodTabsContent">
    {{-- TAB: MÔ TẢ — chỉ hiển thị mô tả --}}
    <div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">
      <h2 class="h5 fw-semibold mb-2">Mô tả sản phẩm</h2>
      <div class="text-secondary">
        {!! nl2br(e($product->description ?? 'Chưa có mô tả cho sản phẩm này.')) !!}
      </div>
    </div>

    {{-- TAB: ĐÁNH GIÁ — chỉ review + form --}}
    <div class="tab-pane fade" id="rvw" role="tabpanel" aria-labelledby="rvw-tab">
      {{-- Tóm tắt điểm --}}
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="display-6 fw-bold text-warning mb-0">{{ number_format($avgRating,1) }}</div>
        <div>
          <div class="text-warning">
            @php $stars = (int) round($avgRating); @endphp
            @for($i=1; $i<=5; $i++)
              <i class="fas fa-star {{ $i <= $stars ? '' : 'text-secondary' }}"></i>
            @endfor
          </div>
          <div class="text-muted small">{{ $totalReview }} đánh giá đã duyệt</div>
        </div>
      </div>

      {{-- Form gửi/cập nhật review (mỗi user 1 review) --}}
      @auth
        @php
          $myReview = $product->reviews()->where('user_id', auth()->id())->first();
        @endphp

        @if(!$myReview)
          <form action="{{ route('reviews.store', $product) }}" method="POST" class="mb-4">
            @csrf
            <div class="row g-3">
              <div class="col-sm-3">
                <label class="form-label">Chọn số sao</label>
                <select name="rating" class="form-select w-auto" required>
                  <option value="">-- Chọn --</option>
                  @for($i=5; $i>=1; $i--)
                    <option value="{{ $i }}">{{ $i }} sao</option>
                  @endfor
                </select>
              </div>
              <div class="col-sm-9">
                <label class="form-label">Bình luận</label>
                <textarea name="comment" rows="3" class="form-control" placeholder="Chia sẻ trải nghiệm của bạn..." required></textarea>
              </div>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Gửi đánh giá
              </button>
            </div>
          </form>
        @else
          <div class="alert alert-success py-2 mb-4">
            ✅ Bạn đã đánh giá sản phẩm này. (Sửa lần nữa: gửi lại form sẽ chuyển trạng thái về chờ duyệt)
          </div>
          {{-- Nếu muốn cho sửa trực tiếp, bạn có thể hiện lại form với giá trị mặc định ở đây --}}
        @endif
      @else
        <div class="alert alert-light border mb-4">
          Bạn cần <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.
        </div>
      @endauth

      {{-- Danh sách đánh giá đã duyệt --}}
      @php
        // Nếu controller đã truyền $reviews (paginate) thì dùng nó, còn không query nhanh:
        $reviews = $reviews ?? $product->approvedReviews()->with('user')->latest()->paginate(8);
      @endphp

      @forelse($reviews as $r)
        <div class="d-flex gap-3 py-3 border-bottom">
          <div class="rounded-circle bg-primary text-white d-inline-grid place-items-center"
               style="width:40px;height:40px;">
            {{ strtoupper(mb_substr($r->user->name ?? 'U', 0, 1)) }}
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold">
                {{ $r->user->name ?? 'Người dùng' }}
                <span class="ms-2 text-warning">
                  @for($i=1; $i<=5; $i++)
                    <i class="fas fa-star {{ $i <= (int)$r->rating ? '' : 'text-secondary' }}"></i>
                  @endfor
                </span>
              </div>
              <div class="text-muted small">{{ optional($r->created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="mt-1 text-muted">{{ $r->comment }}</div>
          </div>
        </div>
      @empty
        <div class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</div>
      @endforelse

      {{-- Phân trang (nếu là LengthAwarePaginator) --}}
      @if(method_exists($reviews, 'links'))
        <div class="d-flex justify-content-center mt-3">
          {{ $reviews->appends(request()->query())->links() }}
        </div>
      @endif
    </div>
  </div>
  {{-- ===================== /TABS ===================== --}}
</div>

{{-- JS: chọn biến thể & đẩy vào form trước khi submit --}}
<script>
  (function(){
    let selected = { color: null, size: null };

    function setActive(groupEl, value) {
      groupEl.querySelectorAll('.attr-pill').forEach(btn => {
        const isActive = btn.dataset.value === value;
        btn.classList.toggle('btn-secondary', isActive);
        btn.classList.toggle('btn-outline-secondary', !isActive);
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });
    }

    document.querySelectorAll('.attr-pill').forEach(btn => {
      btn.addEventListener('click', function () {
        const attr  = this.dataset.attr;  // "color" | "size"
        const value = this.dataset.value;
        selected[attr] = value;
        const groupId = attr === 'color' ? '#color-group' : '#size-group';
        const groupEl = document.querySelector(groupId);
        if (groupEl) setActive(groupEl, value);
      });
    });

    window.injectQtyAndOptions = function(form, requireColor, requireSize) {
      const qtyInput  = document.getElementById('qty');
      const hiddenQty = form.querySelector('input[name="quantity"]');
      if (qtyInput && hiddenQty) {
        const q = Math.max(1, parseInt(qtyInput.value || '1', 10));
        hiddenQty.value = q;
      }
      form.querySelector('input[name="color"]')?.setAttribute('value', selected.color ?? '');
      form.querySelector('input[name="size"]')?.setAttribute('value', selected.size ?? '');

      if (requireColor && !selected.color) { alert('Vui lòng chọn màu.'); return false; }
      if (requireSize && !selected.size)   { alert('Vui lòng chọn size.'); return false; }
      return true;
    };
  })();
</script>

{{-- CSS nhỏ --}}
<style>
  .object-cover { object-fit: cover; }
  .nav-tabs .nav-link { font-weight: 600; }
</style>
@endsection
