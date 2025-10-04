{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Danh sách đơn hàng')

@section('content')
    <h1>Danh sách đơn hàng</h1>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
        <p>Không có đơn hàng nào.</p>
    @else
        @php
            $user = auth()->user();
            $isAdmin = $user && ($user->role === 'admin' || ($user->is_admin ?? false));

            // Badge màu theo trạng thái
            $badge = [
                'pending'    => 'badge bg-secondary',
                'processing' => 'badge bg-warning text-dark',
                'confirmed'  => 'badge bg-primary',
                'cancelled'  => 'badge bg-danger',
                'completed'  => 'badge bg-success',
            ];
        @endphp

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Tên khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $totalDisplay = number_format(
                            $order->total_amount > 0 ? $order->total_amount : ($order->total ?? 0),
                            0, ',', '.'
                        );
                        $statusClass = $badge[$order->status] ?? 'badge bg-secondary';
                    @endphp
                    <tr>
                        <td>#{{ $order->id }}</td>

                        <td>
                            @if($isAdmin)
                                {{ $order->name ?? 'Không rõ' }}
                            @else
                                {{ $user->name }}
                            @endif
                        </td>

                        <td>{{ $totalDisplay }} đ</td>
                        <td><span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>

                        <td class="d-flex flex-wrap gap-1">
                            {{-- Xem chi tiết --}}
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">
                                Xem chi tiết
                            </a>

                            {{-- Admin thao tác --}}
                            @if($isAdmin)
                                @if(in_array($order->status, ['pending','processing']))
                                    {{-- Duyệt --}}
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
                                    <form action="{{ route('orders.update', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc muốn hủy đơn #{{ $order->id }}?')">
                                            Hủy
                                        </button>
                                    </form>
                                @elseif($order->status === 'confirmed')
                                    {{-- Hoàn tất --}}
                                    <form action="{{ route('orders.update', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary btn-sm"
                                                onclick="return confirm('Xác nhận hoàn tất đơn #{{ $order->id }}?')">
                                            Hoàn tất
                                        </button>
                                    </form>
                                @endif

                                {{-- Xóa đơn hàng --}}
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Xóa đơn hàng #{{ $order->id }}?')">
                                        Xóa
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('welcome') }}" class="btn btn-secondary">Quay lại</a>
@endsection
