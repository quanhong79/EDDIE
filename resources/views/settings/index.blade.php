@extends('layouts.app')

@section('title', 'Cài đặt')

@section('content')
<div class="container-fluid">
  <div class="row">
    {{-- Menu bên trái --}}
    <div class="col-md-3 mb-3">
      <div class="list-group">
        <a href="#tab-profile" class="list-group-item list-group-item-action active" data-toggle="list">Hồ sơ</a>
        <a href="#tab-payment" class="list-group-item list-group-item-action" data-toggle="list">Thanh toán</a>
        <a href="#tab-notify" class="list-group-item list-group-item-action" data-toggle="list">Thông báo</a>
        <a href="#tab-language" class="list-group-item list-group-item-action" data-toggle="list">Ngôn ngữ</a>
        <a href="#tab-security" class="list-group-item list-group-item-action" data-toggle="list">Đổi mật khẩu</a>
        <a href="#tab-support" class="list-group-item list-group-item-action" data-toggle="list">Trung tâm hỗ trợ</a>
      </div>
    </div>

    {{-- Nội dung chính --}}
    <div class="col-md-9">
      {{-- Hiển thị flash message --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="tab-content">

        {{-- Hồ sơ --}}
        <div class="tab-pane fade show active" id="tab-profile">
          <h4 class="mb-3">Hồ sơ cá nhân</h4>
          <form action="{{ route('settings.profile.update') }}" method="POST">
            @csrf @method('PATCH')

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Tên</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $user->name) }}" required>
              </div>
              <div class="form-group col-md-6">
                <label>Email (không thay đổi tại đây)</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control" 
                       value="{{ old('phone', $user->phone) }}">
              </div>
              <div class="form-group col-md-6">
                <label>Tỉnh/Thành</label>
                <input type="text" name="city" class="form-control" 
                       value="{{ old('city', $user->city) }}">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Quận/Huyện</label>
                <input type="text" name="district" class="form-control" 
                       value="{{ old('district', $user->district) }}">
              </div>
              <div class="form-group col-md-6">
                <label>Quốc gia</label>
                <input type="text" name="country" class="form-control" 
                       value="{{ old('country', $user->country ?? 'VN') }}">
              </div>
            </div>

            <div class="form-group">
              <label>Địa chỉ</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
            </div>

            <button class="btn btn-dark">Lưu hồ sơ</button>
          </form>
        </div>

        {{-- Thanh toán --}}
        <div class="tab-pane fade" id="tab-payment">
          <h4 class="mb-3">Cài đặt thanh toán</h4>
          <form action="{{ route('settings.payment.update') }}" method="POST">
            @csrf @method('PATCH')
            @php $pm = old('default_payment_method', $user->default_payment_method ?? 'COD'); @endphp
            <div class="form-group">
              <label>Phương thức mặc định</label>
              <select name="default_payment_method" class="form-control">
                <option value="COD"   {{ $pm === 'COD' ? 'selected' : '' }}>COD (khi nhận hàng)</option>
                <option value="VNPAY" {{ $pm === 'VNPAY' ? 'selected' : '' }}>VNPay</option>
                <option value="CARD"  {{ $pm === 'CARD' ? 'selected' : '' }}>Thẻ ngân hàng</option>
              </select>
            </div>
            <button class="btn btn-dark">Lưu</button>
          </form>
        </div>

        {{-- Thông báo --}}
        <div class="tab-pane fade" id="tab-notify">
          <h4 class="mb-3">Cài đặt thông báo</h4>
          <form action="{{ route('settings.notifications.update') }}" method="POST">
            @csrf @method('PATCH')
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="notify_email" name="notify_email" value="1"
                     {{ old('notify_email', $user->notify_email) ? 'checked' : '' }}>
              <label class="form-check-label" for="notify_email">Nhận thông báo qua Email</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="notify_sms" name="notify_sms" value="1"
                     {{ old('notify_sms', $user->notify_sms) ? 'checked' : '' }}>
              <label class="form-check-label" for="notify_sms">Nhận thông báo qua SMS</label>
            </div>
            <button class="btn btn-dark mt-2">Lưu</button>
          </form>
        </div>

        {{-- Ngôn ngữ --}}
        <div class="tab-pane fade" id="tab-language">
          <h4 class="mb-3">Ngôn ngữ</h4>
          <form action="{{ route('settings.language.update') }}" method="POST">
            @csrf @method('PATCH')
            @php $lang = old('language', $user->language ?? 'vi'); @endphp
            <select name="language" class="form-control" style="max-width:240px;">
              <option value="vi" {{ $lang === 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
              <option value="en" {{ $lang === 'en' ? 'selected' : '' }}>English</option>
            </select>
            <button class="btn btn-dark mt-2">Lưu</button>
          </form>
        </div>

        {{-- Đổi mật khẩu --}}
        <div class="tab-pane fade" id="tab-security">
          <h4 class="mb-3">Đổi mật khẩu</h4>
          <form action="{{ route('settings.password.update') }}" method="POST" autocomplete="off">
            @csrf @method('PATCH')

            <div class="form-group">
              <label>Mật khẩu hiện tại</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Mật khẩu mới</label>
              <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group">
              <label>Nhập lại mật khẩu mới</label>
              <input type="password" name="password_confirmation" class="form-control" required minlength="6">
            </div>

            <button class="btn btn-dark">Cập nhật mật khẩu</button>
          </form>
        </div>

        {{-- Trung tâm hỗ trợ --}}
        <div class="tab-pane fade" id="tab-support">
          <h4 class="mb-3">Trung tâm hỗ trợ</h4>
          <p class="text-muted">📞 Hotline: 0964 942 121</p>
          <p class="text-muted">✉️ Email: cskh@myshop.vn</p>
          <p class="text-muted">🕒 Giờ hỗ trợ: 8:00–21:00 (Thứ 2 – Chủ nhật)</p>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
