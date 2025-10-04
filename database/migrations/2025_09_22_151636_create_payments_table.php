<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('method', 30)->index();        // BANK_MANUAL | VNPAY | COD ...
            $table->string('bank_code', 50)->nullable();
            $table->string('payer_name', 120)->nullable();
            $table->string('reference_no', 100)->nullable(); // mã giao dịch/Ref
            $table->unsignedBigInteger('amount')->nullable(); // VND
            $table->string('status', 20)->default('submitted'); // submitted|verified|rejected
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
