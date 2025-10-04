<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vnpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('vnp_TxnRef', 64)->index();
            $table->bigInteger('vnp_Amount')->nullable(); // VND x100
            $table->string('vnp_BankCode', 50)->nullable();
            $table->string('vnp_BankTranNo', 100)->nullable();
            $table->string('vnp_CardType', 50)->nullable();
            $table->text('vnp_OrderInfo')->nullable();
            $table->string('vnp_PayDate', 20)->nullable();
            $table->string('vnp_ResponseCode', 10)->nullable();
            $table->string('vnp_TransactionNo', 50)->nullable();
            $table->string('vnp_TransactionStatus', 10)->nullable();
            $table->text('vnp_SecureHash')->nullable();
            $table->longText('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnpay_transactions');
    }
};
