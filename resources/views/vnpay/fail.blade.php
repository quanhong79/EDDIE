<h2 style="color:red;">Giao dịch thất bại!</h2>
@if(!empty($error_message))
    <p>Lý do: {{ $error_message }}</p>
@endif
<pre>{{ print_r($data ?? [], true) }}</pre>
