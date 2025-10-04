@extends('layouts.app')
@section('title','Cảm ơn')
@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded-2xl">
  <h1 class="text-xl font-semibold mb-2">Cảm ơn bạn!</h1>
  @if (session('success'))
    <p class="text-green-700">{{ session('success') }}</p>
  @else
    <p>Đơn hàng của bạn đã được ghi nhận và đang chờ duyệt.</p>
  @endif
  <a href="{{ route('orders.index') }}" class="inline-block mt-4 text-blue-600 hover:underline">Xem đơn hàng</a>
</div>
@endsection
