<h2 style="color:green;">Giao dịch thành công!</h2>
<ul>
    <li>Mã giao dịch: {{ $data['vnp_TxnRef'] }}</li>
    <li>Số tiền: {{ number_format($data['vnp_Amount'] / 100, 0, ',', '.') }} VND</li>
    <li>Thời gian: {{ $data['vnp_PayDate'] }}</li>
</ul>
