{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
  <h1 class="mb-3">Chi tiết đơn hàng {{ $order->code ? '#'.$order->code : '#'.$order->id }}</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @php
    $authUser = auth()->user();
    $isAdmin = $authUser && ($authUser->role === 'admin' || ($authUser->is_admin ?? false));

    // Tổng tiền: ưu tiên total_amount (int VNĐ), fallback total (decimal)
    $totalInt = (int) ($order->total_amount ?? 0);
    $totalDec = (float) ($order->total ?? 0.0);
    $totalDisplay = number_format($totalInt > 0 ? $totalInt : $totalDec, 0, ',', '.');

    // Thông tin khách hàng/nhận hàng
    $shipName    = $order->shipping_name   ?? null;
    $shipPhone   = $order->shipping_phone  ?? null;
    $shipAddress = $order->shipping_address?? null;

    $userName    = optional($order->user)->name;
    $userEmail   = optional($order->user)->email ?? '—';
    $userPhone   = optional($order->user)->phone ?? null;     // nếu users có cột phone
    $userAddress = optional($order->user)->address ?? null;   // nếu users có cột address

    $firstPayment = optional($order->payments)[0] ?? null;
    $payerName    = $firstPayment->payer_name ?? null;

    $customerName    = $shipName    ?? $userName    ?? $payerName ?? 'Không rõ';
    $customerPhone   = $shipPhone   ?? $userPhone   ?? '—';
    $customerAddress = $shipAddress ?? $userAddress ?? '—';
    $customerEmail   = $userEmail;

    // Phương thức thanh toán (schema có cả payment_method và payment)
    $payMethod = $order->payment_method ?? $order->payment ?? '—';

    // Trạng thái → badge
    $badge = [
      'pending'    => 'badge bg-warning text-dark',
      'processing' => 'badge bg-info text-dark',
      'confirmed'  => 'badge bg-primary',
      'cancelled'  => 'badge bg-danger',
      'completed'  => 'badge bg-success',
    ];
    $statusClass = $badge[$order->status] ?? 'badge bg-secondary';
  @endphp

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Thông tin đơn hàng</h5>

      <div class="row mb-3">
        <div class="col-md-6">
          <p><strong>Tên khách hàng:</strong> {{ $customerName }}</p>
          <p><strong>Email:</strong> {{ $customerEmail }}</p>
          <p><strong>Số điện thoại:</strong> {{ $customerPhone }}</p>
          <p><strong>Địa chỉ:</strong> {{ $customerAddress }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Mã đơn:</strong> {{ $order->code ?? ('#'.$order->id) }}</p>
          <p><strong>Ngày tạo:</strong> {{ optional($order->created_at)->format('d/m/Y H:i') }}</p>
          <p><strong>Tổng tiền:</strong> {{ $totalDisplay }} đ</p>
          <p><strong>Phương thức thanh toán:</strong> {{ $payMethod }}</p>
          <p><strong>Trạng thái:</strong> <span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span></p>

          {{-- Chỉ admin mới có quyền cập nhật trạng thái --}}
          @if($isAdmin)
            <div class="d-flex flex-wrap gap-2 mt-2">
              @if (in_array($order->status, ['pending','processing']))
                {{-- Xác nhận --}}
                <form action="{{ route('orders.update', $order->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="confirmed">
                  <button type="submit" class="btn btn-success btn-sm"
                          onclick="return confirm('Xác nhận duyệt đơn #{{ $order->id }}?')">
                    Xác nhận
                  </button>
                </form>

                {{-- Hủy --}}
                <form action="{{ route('orders.update', $order->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Bạn có chắc muốn hủy đơn #{{ $order->id }}?');">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="cancelled">
                  <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                </form>
              @elseif ($order->status === 'confirmed')
                {{-- Hoàn tất --}}
                <form action="{{ route('orders.update', $order->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Đánh dấu hoàn tất đơn #{{ $order->id }}?');">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="completed">
                  <button type="submit" class="btn btn-success btn-sm">Hoàn tất</button>
                </form>
              @endif
            </div>
          @endif
        </div>
      </div>

      <h5 class="card-title mt-3">Danh sách sản phẩm</h5>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th class="text-center">Số lượng</th>
              <th class="text-end">Đơn giá</th>
              <th class="text-end">Thành tiền</th>
            </tr>
          </thead>
          <tbody>
          @foreach($order->orderItems as $item)
            @php
              $name  = $item->product->name ?? 'Sản phẩm không xác định';
              $qty   = (int) $item->quantity;
              $price = (float) $item->price;
            @endphp
            <tr>
              <td>{{ $name }}</td>
              <td class="text-center">{{ $qty }}</td>
              <td class="text-end">{{ number_format($price, 0, ',', '.') }} đ</td>
              <td class="text-end">{{ number_format($qty * $price, 0, ',', '.') }} đ</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      {{-- Mua lại --}}
      <form action="{{ route('orders.reorder', $order->id) }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-primary">Mua lại</button>
      </form>

      <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
    </div>
  </div>
@endsection
