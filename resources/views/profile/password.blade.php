@extends('layouts.app')
@section('title','Đổi mật khẩu')

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header fw-bold">Đổi mật khẩu</div>
      <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form method="POST" action="{{ route('profile.password.update') }}">
          @csrf @method('PATCH')

          <div class="mb-3">
            <label class="form-label">Mật khẩu hiện tại</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu mới</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>

          <button class="btn btn-dark">Cập nhật mật khẩu</button>
          <a class="btn btn-outline-secondary" href="{{ route('profile.edit') }}">Về trang hồ sơ</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
