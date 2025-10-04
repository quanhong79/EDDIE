@extends('layouts.app')
@section('title','Thông tin cá nhân')

@section('content')
<div class="row">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-bold">Cập nhật thông tin</div>
      <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('profile.update') }}">
          @csrf @method('PATCH')

          <div class="mb-3">
            <label class="form-label">Họ & Tên</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name',$user->name) }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email (không đổi tại đây)</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone',$user->phone) }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                   value="{{ old('address',$user->address) }}">
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Thành phố</label>
              <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                     value="{{ old('city',$user->city) }}">
              @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tỉnh/TP</label>
              <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                     value="{{ old('state',$user->state) }}">
              @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Mã bưu chính</label>
            <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                   value="{{ old('postal_code',$user->postal_code) }}">
            @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <button class="btn btn-dark">Lưu thay đổi</button>
          <a class="btn btn-outline-secondary" href="{{ url()->previous() }}">Hủy</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
