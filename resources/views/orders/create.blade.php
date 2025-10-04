@extends('layouts.app')

@section('title', 'Thông tin đặt hàng')

@section('content')
    <h1>Thông tin đặt hàng</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(!empty($cart) && is_array($cart))
        <h3>Chi tiết giỏ hàng</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $calculatedTotal = 0; @endphp
                @foreach($cart as $id => $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $calculatedTotal += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right">
            <h4>Tổng cộng: {{ number_format($calculatedTotal, 0, ',', '.') }} đ</h4>
        </div>
    @else
        <p>Giỏ hàng trống.</p>
    @endif

    <form action="{{ route('order.store') }}" method="POST">
        @csrf
        <input type="hidden" name="total" value="{{ $calculatedTotal }}">
        <input type="hidden" name="cart" value="{{ json_encode($cart) }}">

        <div class="form-group">
            <label for="name">Họ và tên:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại:</label>
            <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">Địa chỉ nhận hàng:</label>
            <textarea name="address" id="address" class="form-control" required>{{ old('address') }}</textarea>
            @error('address')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="payment_method">Phương thức thanh toán:</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control" value="COD" readonly>
        </div>

        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
        <a href="{{ route('cart.index') }}" class="btn btn-secondary">Quay lại giỏ hàng</a>
    </form>
@endsection