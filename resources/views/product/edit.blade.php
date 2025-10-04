@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<h1>Edit Product</h1>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label>Tên</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
  </div>

  <div class="mb-3">
    <label>Mô tả</label>
    <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
  </div>

  <div class="mb-3">
    <label>Giá</label>
    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
  </div>

  {{-- Ảnh hiện tại --}}
  <div class="mb-3">
    <label class="d-block mb-1">Ảnh hiện tại</label>
    @if($product->image)
      <img id="currentPreview" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
           style="width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
    @else
      <div class="text-muted">Chưa có ảnh</div>
    @endif
  </div>

  {{-- Chọn ảnh mới (tuỳ chọn) --}}
  <div class="mb-3">
    <label>Đổi ảnh (tuỳ chọn)</label>
    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewNewImage(event)">
    <small class="text-muted d-block">Nếu không chọn, ảnh cũ sẽ được giữ nguyên.</small>
    <div class="mt-2">
      <img id="newPreview" style="display:none;width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
    </div>
  </div>

  {{-- Tuỳ chọn xoá ảnh hiện tại --}}
  @if($product->image)
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" value="1" id="removeImage" name="remove_image">
    <label class="form-check-label" for="removeImage">
      Xoá ảnh hiện tại
    </label>
  </div>
  @endif

  <button class="btn btn-primary">Cập nhật</button>
</form>

<script>
function previewNewImage(e){
  const file = e.target.files?.[0];
  const img = document.getElementById('newPreview');
  if(file){
    img.src = URL.createObjectURL(file);
    img.style.display = 'inline-block';
  } else {
    img.src = '';
    img.style.display = 'none';
  }
}
</script>
@endsection
