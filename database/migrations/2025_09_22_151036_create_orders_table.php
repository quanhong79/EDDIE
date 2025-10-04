<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Người dùng đặt đơn
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Mã đơn hàng (unique)
            $table->string('code', 32)->unique();

            // Tổng tiền (lưu snapshot tại thời điểm đặt)
            $table->decimal('total', 12, 2)->default(0);

            // Trạng thái đơn hàng
            $table->string('status', 20)->default('processing'); 
            // processing | confirmed | completed | cancelled

            // Phương thức thanh toán
            $table->string('payment', 20)->default('COD'); 
            // COD | VNPAY | BANK_TRANSFER

            // (Tùy chọn) Trạng thái thanh toán online
            $table->string('payment_status', 20)->default('pending');
            // pending | paid | failed | unpaid

            // (Tùy chọn) cart_key cho khách
            $table->string('cart_key', 64)->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
