{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Thanh toán</h1>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @php
    // fallback nếu controller không truyền
    $cart       = isset($cart) ? collect($cart) : collect(session('cart', []));
    $subTotal   = $subTotal   ?? $cart->sum(fn($i)=>($i['price']??0)*($i['qty']??1));
    $shipping   = $shipping   ?? 0;
    $discount   = $discount   ?? 0;
    $grandTotal = $grandTotal ?? max(0, $subTotal - $discount + $shipping);

    function vnd($n){ return number_format((float)$n, 0, ',', '.') . ' đ'; }
  @endphp
  {{-- Thông tin giao hàng --}}
<div class="mb-3">
  <label class="form-label">Họ tên người nhận <span class="text-danger">*</span></label>
  <input type="text" name="shipping_name" class="form-control" required
         value="{{ old('shipping_name', auth()->user()->name ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
  <input type="tel" name="shipping_phone" class="form-control" required
         pattern="^[0-9]{9,11}$"
         placeholder="Ví dụ: 0901234567"
         value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}">
  <small class="text-muted">Chỉ số, 9–11 ký tự.</small>
</div>

<div class="mb-3">
  <label class="form-label">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
  <textarea name="shipping_address" class="form-control" rows="2" required
            placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">
{{ old('shipping_address', auth()->user()->address ?? '') }}</textarea>
</div>
  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header fw-bold">Chọn phương thức thanh toán</div>
        <div class="card-body">
          <form id="payForm" action="{{ route('checkout.cod') }}" method="POST">
            @csrf

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="pay_method" id="pm_cod" value="cod" checked>
              <label class="form-check-label" for="pm_cod">Thanh toán khi nhận hàng (COD)</label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="pay_method" id="pm_vnpay" value="vnpay">
              <label class="form-check-label" for="pm_vnpay">VNPay (thẻ/QR)</label>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="pay_method" id="pm_vietqr" value="vietqr">
              <label class="form-check-label" for="pm_vietqr">Chuyển khoản/VietQR</label>
            </div>

            {{-- Ghi chú đơn hàng --}}
            <div class="mb-3">
              <label class="form-label" for="note">Ghi chú</label>
              <textarea class="form-control" name="note" id="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
              Xác nhận & tiếp tục
            </button>
          </form>
        </div>
      </div>

      {{-- Hiển thị tóm tắt giỏ hàng --}}
      <div class="card">
        <div class="card-header fw-bold">Sản phẩm</div>
        <div class="card-body p-0">
          <table class="table table-borderless align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Sản phẩm</th>
                <th class="text-center" style="width:120px;">SL</th>
                <th class="text-end" style="width:160px;">Thành tiền</th>
              </tr>
            </thead>
            <tbody>
            @foreach($cart as $item)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if(!empty($item['image']))
                      <img src="{{ asset('storage/'.$item['image']) }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $item['name'] ?? 'Sản phẩm' }}</div>
                      @if(!empty($item['options']))
                        <small class="text-muted">
                          {{ is_array($item['options']) ? collect($item['options'])->map(fn($v,$k)=>"$k: $v")->join(', ') : $item['options'] }}
                        </small>
                      @endif
                      <div><small class="text-muted">Giá: {{ vnd($item['price'] ?? 0) }}</small></div>
                    </div>
                  </div>
                </td>
                <td class="text-center">{{ $item['qty'] ?? 1 }}</td>
                <td class="text-end">{{ vnd(($item['price'] ?? 0) * ($item['qty'] ?? 1)) }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header fw-bold">Tổng kết</div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính</span><span>{{ vnd($subTotal) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Giảm giá</span><span>- {{ vnd($discount) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Phí vận chuyển</span><span>{{ vnd($shipping) }}</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fs-5 fw-bold">
            <span>Thanh toán</span><span>{{ vnd($grandTotal) }}</span>
          </div>
          <small class="text-muted d-block mt-2">* Số tiền cuối cùng có thể đổi khi áp dụng mã giảm giá / phí ship thực tế.</small>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- JS đổi action form theo phương thức --}}
<script>
  (function () {
    const form = document.getElementById('payForm');
    const routes = {
      cod:    "{{ route('checkout.cod') }}",
      vnpay:  "{{ route('checkout.vnpay') }}",
      vietqr: "{{ route('checkout.vietqr') }}", // nếu chưa làm thì tạm để route COD để test
    };
    function updateAction() {
      const val = document.querySelector('input[name="pay_method"]:checked')?.value || 'cod';
      form.action = routes[val] || routes.cod;
    }
    document.querySelectorAll('input[name="pay_method"]').forEach(r => {
      r.addEventListener('change', updateAction);
    });
    updateAction(); // khởi tạo
  })();
</script>
@endsection
