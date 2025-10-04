@php
  /*
   |-------------------------------------------------------------
   | XÁC ĐỊNH SLUG DANH MỤC HIỆN TẠI & NGUỒN DỮ LIỆU DANH MỤC
   |-------------------------------------------------------------
   | - $currentSlug: lấy từ route('category') (model/slug) hoặc $category nếu có.
   | - $categoriesForFilter: ưu tiên $categories, fallback $allCategories.
   */

  // 1) Lấy tham số category hiện tại (model hoặc slug)
  $routeParam  = request()->route('category') ?? ($category ?? null);
  $currentSlug = null;

  if ($routeParam instanceof \Illuminate\Database\Eloquent\Model) {
      // Nếu là model (binding theo slug hoặc id)
      $currentSlug = (string) $routeParam->getRouteKey();
  } elseif (is_object($routeParam) && isset($routeParam->slug)) {
      // Nếu là object thường có thuộc tính slug
      $currentSlug = (string) $routeParam->slug;
  } elseif (is_scalar($routeParam) && $routeParam !== '') {
      // Nếu là string/int slug
      $currentSlug = (string) $routeParam;
  }

  // 2) Chuẩn hoá danh sách danh mục để render filter
  $rawCats = $categories ?? ($allCategories ?? []);
  $categoriesForFilter = $rawCats instanceof \Illuminate\Support\Collection ? $rawCats : collect($rawCats);

  // Loại bỏ phần tử rỗng, giữ nguyên thứ tự
  $categoriesForFilter = $categoriesForFilter
      ->filter(fn($c) => !is_null($c) && $c !== '')
      ->values();
@endphp


<form id="product-filter" method="GET" action="{{ url()->current() }}" class="mb-3">
  <div class="d-flex flex-wrap align-items-center" style="gap:.5rem 1rem;">
    @if (!request()->routeIs('product.index') && !request()->routeIs('category.show'))
  @php return; @endphp
@endif
    {{-- Tìm theo tên --}}
    <div class="input-group input-group-sm" style="max-width:260px;">
      <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}">
      <button class="btn btn-outline-secondary" type="submit">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </div>

    {{-- DANH MỤC --}}
    <div class="input-group input-group-sm" style="max-width:300px;">
      <span class="input-group-text">Danh mục</span>
      <select class="form-select" id="filter-category">
        {{-- Tất cả sản phẩm --}}
        <option
          value=""
          data-url="{{ route('product.index') }}"
          @selected(!$currentSlug)
        >
          Tất cả sản phẩm
        </option>

        @foreach($categoriesForFilter as $cat)
          @php
            $key = $cat instanceof \Illuminate\Database\Eloquent\Model
                     ? (string) $cat->getRouteKey()
                     : (string) ($cat->slug ?? $cat->id ?? '');
            if ($key === '') continue;

            // URL đúng cho danh mục này (dùng route binding)
            $catUrl = route('category.show', ['category' => $key]);
          @endphp
          <option
            value="{{ $key }}"
            data-url="{{ $catUrl }}"
            @selected($currentSlug === $key)
          >
            {{ $cat->name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Khoảng giá --}}
    <div class="input-group input-group-sm" style="max-width:220px;">
      <span class="input-group-text">Từ</span>
      <input type="number" name="min" class="form-control" min="0" step="1000"
             value="{{ request('min') }}" placeholder="0">
    </div>

    <div class="input-group input-group-sm" style="max-width:220px;">
      <span class="input-group-text">Đến</span>
      <input type="number" name="max" class="form-control" min="0" step="1000"
             value="{{ request('max') }}" placeholder="1000000">
    </div>

    {{-- Sắp xếp --}}
    <div class="input-group input-group-sm" style="max-width:260px;">
      <span class="input-group-text">Sắp xếp</span>
      @php $sort = request('sort','new'); @endphp
      <select name="sort" class="form-select" id="filter-sort">
        <option value="new"        @selected($sort==='new')>Mới nhất</option>
        <option value="price_asc"  @selected($sort==='price_asc')>Giá: thấp → cao</option>
        <option value="price_desc" @selected($sort==='price_desc')>Giá: cao → thấp</option>
      </select>
    </div>

    {{-- Nút xoá lọc (reset về URL hiện tại không query) --}}
    <a href="{{ url()->current() }}" class="btn btn-link btn-sm">Xoá lọc</a>
  </div>
</form>

<script>
(function() {
  const form = document.getElementById('product-filter');
  const sel  = document.getElementById('filter-category');
  const sort = document.getElementById('filter-sort');

  // Lấy params hiện tại từ các input trong form (ưu tiên giá trị người dùng đang gõ)
  function getLiveParams() {
    const params = new URLSearchParams();

    const q   = form.querySelector('input[name="q"]')?.value?.trim();
    const min = form.querySelector('input[name="min"]')?.value?.trim();
    const max = form.querySelector('input[name="max"]')?.value?.trim();
    const srt = form.querySelector('select[name="sort"]')?.value;

    if (q)   params.set('q', q);
    if (min) params.set('min', min);
    if (max) params.set('max', max);
    if (srt && srt !== 'new') params.set('sort', srt); // 'new' là mặc định → không cần đưa vào

    return params.toString();
  }

  // Khi đổi danh mục → điều hướng đúng URL danh mục, kèm query hiện tại
  sel?.addEventListener('change', () => {
    const toBase = sel.options[sel.selectedIndex]?.getAttribute('data-url') || '{{ route('product.index') }}';
    const qs = getLiveParams();
    const next = qs ? (toBase + (toBase.includes('?') ? '&' : '?') + qs) : toBase;
    window.location.href = next;
  });

  // Khi đổi sort → submit form GET tại chỗ (giữ nguyên URL hiện tại)
  sort?.addEventListener('change', () => form.submit());
})();
</script>
