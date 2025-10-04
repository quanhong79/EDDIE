{{-- resources/views/checkout/vnpay_shim.blade.php --}}
<form id="vnpay-auto-post" method="POST" action="{{ route('checkout.vnpay') }}">
  @csrf
</form>
<script>document.getElementById('vnpay-auto-post').submit();</script>
<noscript>
  <button form="vnpay-auto-post">Tiếp tục</button>
</noscript>
