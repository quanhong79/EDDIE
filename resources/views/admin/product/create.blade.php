@extends('layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container py-3">
  <h1 class="mb-4">Thêm sản phẩm mới</h1>

  {{-- Hiển thị lỗi validate --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Tên sản phẩm --}}
    <div class="mb-3">
      <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control"
             value="{{ old('name') }}" required>
    </div>

    {{-- Mô tả --}}
    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>

    <div class="row">
      {{-- Giá --}}
      <div class="col-md-4 mb-3">
        <label class="form-label">Giá (₫) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="price" class="form-control"
               value="{{ old('price') }}" required>
      </div>

      {{-- Số lượng --}}
      <div class="col-md-4 mb-3">
        <label class="form-label">Số lượng <span class="text-danger">*</span></label>
        <input type="number" min="0" name="quantity" class="form-control"
               value="{{ old('quantity', 0) }}" required>
      </div>

      {{-- Danh mục --}}
      <div class="col-md-4 mb-3">
        <label class="form-label">Danh mục</label>
        @php
          $parents = isset($parents)
            ? $parents
            : \App\Models\Category::with('children')->whereNull('parent_id')->get();
        @endphp
        <select name="category_id" class="form-control">
          <option value="">-- Chọn danh mục --</option>
          @foreach ($parents as $p)
            @if($p->children->count())
              <optgroup label="{{ $p->name }}">
                @foreach($p->children as $child)
                  <option value="{{ $child->id }}" @selected(old('category_id') == $child->id)>
                    {{ $child->name }}
                  </option>
                @endforeach
              </optgroup>
            @else
              <option value="{{ $p->id }}" @selected(old('category_id') == $p->id)>
                {{ $p->name }}
              </option>
            @endif
          @endforeach
        </select>
      </div>
    </div>

    {{-- KIỂU SIZE --}}
    @php
      $sizeMode = old('size_mode', 'none'); // none | apparel | shoes
    @endphp
    <div class="mb-3">
      <label class="form-label fw-semibold d-block">Kiểu size</label>
      <div class="d-flex gap-3 flex-wrap">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="size_mode" value="none" id="szNone" @checked($sizeMode==='none')>
          <label class="form-check-label" for="szNone">Không dùng size</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="size_mode" value="apparel" id="szApparel" @checked($sizeMode==='apparel')>
          <label class="form-check-label" for="szApparel">Quần áo (S–XXL)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="size_mode" value="shoes" id="szShoes" @checked($sizeMode==='shoes')>
          <label class="form-check-label" for="szShoes">Giày (35–46)</label>
        </div>
      </div>
    </div>

    {{-- SIZE QUẦN ÁO --}}
    @php
      $allSizes = ['S','M','L','XL','XXL'];
      $currentSizes = (array) old('sizes', []);
    @endphp
    <div class="mb-3 js-apparel-sizes">
      <label class="form-label d-block">Sizes (S/M/L/XL/XXL)</label>
      <div class="d-flex flex-wrap gap-2">
        @foreach ($allSizes as $sz)
          <label class="btn btn-outline-dark btn-sm mb-0">
            <input type="checkbox" name="sizes[]" value="{{ $sz }}" class="me-1"
                   {{ in_array($sz, $currentSizes, true) ? 'checked' : '' }}>
            {{ $sz }}
          </label>
        @endforeach
      </div>
      <small class="text-muted d-block mt-1">Chỉ dùng khi chọn kiểu size "Quần áo".</small>
    </div>

    {{-- SIZE GIÀY --}}
    @php
      $shoeOld = array_map('intval', (array) old('shoe_sizes', []));
    @endphp
    <div class="mb-3 js-shoe-sizes">
      <label class="form-label d-block">Sizes giày (35–46)</label>
      <div class="d-flex flex-wrap gap-2">
        @for($i=35; $i<=46; $i++)
          <label class="btn btn-outline-dark btn-sm mb-0">
            <input type="checkbox" name="shoe_sizes[]" value="{{ $i }}" class="me-1"
                   {{ in_array($i, $shoeOld, true) ? 'checked' : '' }}>
            {{ $i }}
          </label>
        @endfor
      </div>
      <small class="text-muted d-block mt-1">Chỉ dùng khi chọn kiểu size "Giày".</small>
    </div>

    {{-- MÀU SẮC --}}
    @php
      $colorsOld = (array) old('colors', []);
    @endphp
    <div class="mb-3">
      <label class="form-label fw-semibold">Màu sắc</label>
      <small class="text-muted d-block mb-1">Nhập nhiều màu: nhấn “+ Thêm màu” để thêm một dòng</small>
      <div id="colorList" class="d-flex flex-column gap-2">
        @forelse($colorsOld as $c)
          <div class="input-group input-group-sm" style="max-width:320px;">
            <input type="text" name="colors[]" class="form-control" value="{{ $c }}" placeholder="VD: Đen">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">–</button>
          </div>
        @empty
          <div class="input-group input-group-sm" style="max-width:320px;">
            <input type="text" name="colors[]" class="form-control" placeholder="VD: Đen">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">–</button>
          </div>
        @endforelse
      </div>
      <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addColor()">+ Thêm màu</button>
    </div>

    {{-- Ảnh đại diện --}}
    <div class="mb-3">
      <label class="form-label">Ảnh đại diện</label>
      <input type="file" name="image" class="form-control" accept="image/*" onchange="previewNewImage(event)">
      <div class="mt-2">
        <img id="newPreview" style="display:none;width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
      </div>
    </div>

    {{-- Thêm ảnh gallery --}}
    <div class="mb-3">
      <label class="form-label">Thêm ảnh gallery (tối đa 4)</label>
      <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple onchange="previewGallery(this)">
      <div id="galleryPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
    </div>

    <button type="submit" class="btn btn-primary">Tạo mới</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>

<script>
function addColor(){
  const wrap = document.getElementById('colorList');
  const div = document.createElement('div');
  div.className = 'input-group input-group-sm';
  div.style.maxWidth = '320px';
  div.innerHTML = `
    <input type="text" name="colors[]" class="form-control" placeholder="VD: Trắng">
    <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">–</button>
  `;
  wrap.appendChild(div);
}

function previewNewImage(e){
  const file = e.target.files?.[0];
  const img = document.getElementById('newPreview');
  if(file){
    img.src = URL.createObjectURL(file);
    img.style.display = 'inline-block';
  }else{
    img.src = '';
    img.style.display = 'none';
  }
}

function previewGallery(input){
  const wrap = document.getElementById('galleryPreview');
  wrap.innerHTML = '';
  const files = Array.from(input.files || []).slice(0, 4);
  files.forEach(f => {
    const url = URL.createObjectURL(f);
    const img = document.createElement('img');
    img.src = url;
    img.style.width = '90px';
    img.style.height = '90px';
    img.style.objectFit = 'cover';
    img.style.borderRadius = '6px';
    img.style.border = '1px solid #eee';
    wrap.appendChild(img);
  });
}

// Ẩn/hiện khối size theo size_mode
function toggleSizeBlocks(){
  const mode = document.querySelector('input[name="size_mode"]:checked')?.value || 'none';
  document.querySelector('.js-apparel-sizes')?.classList.toggle('d-none', mode!=='apparel');
  document.querySelector('.js-shoe-sizes')?.classList.toggle('d-none', mode!=='shoes');
}
document.querySelectorAll('input[name="size_mode"]').forEach(r => r.addEventListener('change', toggleSizeBlocks));
document.addEventListener('DOMContentLoaded', toggleSizeBlocks);
</script>
@endsection
