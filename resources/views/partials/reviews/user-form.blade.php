{{-- resources/views/partials/reviews/user-form.blade.php --}}
@auth
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body">
    <h5 class="mb-3">Viết đánh giá của bạn</h5>

    <form action="{{ route('reviews.store', $product->id) }}" method="POST" id="userReviewForm">
      @csrf

      {{-- Star rating --}}
      <div class="mb-3">
        <label class="form-label d-block">Đánh giá</label>
        <div class="rating-input" role="radiogroup" aria-label="Chọn số sao">
          @for($i=5; $i>=1; $i--)
            <input type="radio" name="rating" id="rt-{{ $i }}" value="{{ $i }}"
                   {{ (int)old('rating', 5) === $i ? 'checked' : '' }}>
            <label for="rt-{{ $i }}" title="{{ $i }} sao">
              <i class="fa-regular fa-star"></i>
              <i class="fa-solid fa-star"></i>
            </label>
          @endfor
        </div>
        @error('rating')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      {{-- Nội dung --}}
      <div class="mb-3">
        <label class="form-label">Nội dung</label>
        <textarea name="comment" class="form-control" rows="3" maxlength="1000" required
                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm...">{{ old('comment') }}</textarea>
        <div class="form-text">
          <span id="rvCount">0</span>/1000 ký tự
        </div>
        @error('comment')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary px-4 rounded-pill">
          <i class="fa-regular fa-paper-plane me-1"></i> Gửi đánh giá
        </button>
        <button type="reset" class="btn btn-outline-secondary rounded-pill">Làm mới</button>
      </div>
    </form>
  </div>
</div>
@else
  <div class="alert alert-info rounded-4">
    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.
  </div>
@endauth

{{-- styles --}}
<style>
.rating-input{
  --star-size: 28px;
  display:inline-flex; gap:6px; direction: rtl; /* hover từ phải sang trái */
}
.rating-input input{ display:none; }
.rating-input label{
  position:relative; width:var(--star-size); height:var(--star-size); cursor:pointer; color:#ffc107;
}
.rating-input label .fa-regular,
.rating-input label .fa-solid{
  position:absolute; inset:0; line-height:var(--star-size); font-size:var(--star-size);
}
.rating-input label .fa-solid{ opacity:0; transition:.15s; }
.rating-input label:hover .fa-solid,
.rating-input label:hover ~ label .fa-solid{ opacity:1; }
.rating-input input:checked ~ label .fa-solid{ opacity:1; }
</style>

{{-- scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ta = document.querySelector('#userReviewForm textarea[name="comment"]');
  const cnt = document.getElementById('rvCount');
  if (ta && cnt){
    const sync = ()=> cnt.textContent = (ta.value||'').length;
    ta.addEventListener('input', sync);
    sync();
  }
});
</script>
