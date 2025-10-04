{{-- resources/views/checkout/bank.blade.php --}}
@extends('layouts.app')
@section('title','Thanh toán online (nội bộ)')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0">Thanh toán online (chuyển khoản)</h5>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('checkout.bank.pay') }}">
          @csrf
          <div class="form-group">
            <label for="bank_code">Ngân hàng</label>
            <input id="bank_code" name="bank_code" class="form-control" placeholder="VD: Vietcombank" required>
          </div>

          <div class="form-group">
            <label for="payer_name">Tên người chuyển</label>
            <input id="payer_name" name="payer_name" class="form-control" placeholder="Họ tên trên lệnh chuyển" required>
          </div>

          <div class="form-group">
            <label for="reference_no">Mã giao dịch/Ref (nếu có)</label>
            <input id="reference_no" name="reference_no" class="form-control" placeholder="VD: FT12345678">
          </div>

          <div class="form-group">
            <label for="note">Ghi chú</label>
            <textarea id="note" name="note" class="form-control" rows="3" placeholder="Nội dung chuyển khoản..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Xác nhận thanh toán</button>
          <a href="{{ route('cart.index') }}" class="btn btn-link btn-block">← Quay lại giỏ hàng</a>
        </form>

        <hr>
        <div class="small text-muted">
          Sau khi xác nhận, hệ thống sẽ tạo đơn <strong>đã thanh toán</strong> và <strong>đang chờ duyệt</strong>.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


{{-- resources/views/checkout/thankyou.blade.php --}}
@extends('layouts.app')
@section('title','Cảm ơn')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3"><i class="fa-solid fa-circle-check fa-3x text-success"></i></div>
        <h4 class="mb-2">Cảm ơn bạn!</h4>
        <p class="text-muted">@if(session('success')) {{ session('success') }} @else Đơn hàng của bạn đã được ghi nhận và đang chờ duyệt. @endif</p>
        <a href="{{ route('orders.index') }}" class="btn btn-primary">Xem đơn hàng</a>
        <a href="{{ route('product.index') }}" class="btn btn-link">Tiếp tục mua sắm</a>
      </div>
    </div>
  </div>
</div>
@endsection


{{-- resources/views/checkout/failed.blade.php --}}
@extends('layouts.app')
@section('title','Thanh toán thất bại')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3"><i class="fa-solid fa-circle-xmark fa-3x text-danger"></i></div>
        <h4 class="mb-2">Rất tiếc, thanh toán chưa thành công</h4>
        <p class="text-muted">@if(session('error')) {{ session('error') }} @else Vui lòng thử lại hoặc chọn phương thức khác. @endif</p>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
        @if(app('router')->has('checkout.bank'))
          <a href="{{ route('checkout.bank') }}" class="btn btn-primary ml-2">Thử thanh toán online (nội bộ)</a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
