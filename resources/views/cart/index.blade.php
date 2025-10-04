{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Giỏ hàng của bạn</h1>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @php
    function vnd($n){ return number_format((float)$n, 0, ',', '.') . ' đ'; }
  @endphp

  @if(!empty($cart) && count($cart) > 0)
    <div class="row">
      <div class="col-lg-8">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Sản phẩm</th>
                <th class="text-center" style="width:120px;">Số lượng</th>
                <th class="text-end" style="width:140px;">Đơn giá</th>
                <th class="text-end" style="width:160px;">Thành tiền</th>
                <th class="text-center" style="width:100px;">Xoá</th>
              </tr>
            </thead>
            <tbody>
              @php $subtotal = 0; @endphp
              @foreach($cart as $item)
                @php
                  $id   = $item['db_id'];
                  $qty  = $item['quantity'];
                  $unit = $item['price'];
                  $line = $unit * $qty;
                  $subtotal += $line;
                @endphp
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ $item['image'] }}"
                           class="me-3"
                           style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                      <div>{{ $item['name'] }}</div>
                    </div>
                  </td>
                  <td class="text-center">
                    <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex justify-content-center">
                        @csrf
                        @method('PATCH') 
                        <input type="number" name="qty" min="1" value="{{ $qty }}"
                              class="form-control form-control-sm text-center" style="width:60px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">
                            Cập nhật
                        </button>
                    </form>
                  </td>
                  <td class="text-end">{{ vnd($unit) }}</td>
                  <td class="text-end fw-bold">{{ vnd($line) }}</td>
                  <td class="text-center">
                    <form action="{{ route('cart.remove', $id) }}" method="POST"
                          onsubmit="return confirm('Xoá sản phẩm này khỏi giỏ?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-danger">Xoá</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3 d-flex justify-content-between">
          <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">← Tiếp tục mua sắm</a>
          <form action="{{ route('cart.clear') }}" method="POST"
                onsubmit="return confirm('Xoá toàn bộ giỏ hàng?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger">Xoá toàn bộ</button>
          </form>
        </div>
      </div>

      <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Tổng kết</h5>
            <ul class="list-group mb-3">
              <li class="list-group-item d-flex justify-content-between">
                <span>Tạm tính</span>
                <strong>{{ vnd($subtotal) }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Phí vận chuyển</span>
                <strong>0 đ</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span class="fw-bold">Tổng cộng</span>
                <span class="fw-bold text-danger">{{ vnd($subtotal) }}</span>
              </li>
            </ul>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
  Tiến hành thanh toán
</a>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">Giỏ hàng của bạn đang trống.</div>
    <a href="{{ route('welcome') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
  @endif
</div>
@endsection
