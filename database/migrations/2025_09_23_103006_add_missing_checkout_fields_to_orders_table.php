<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $table) {
            // Cho phép đơn guest (nếu bạn dùng guest checkout)
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }

            // Tổng tiền đơn (VND). Chọn 1 trong 2 kiểu:
            // 1) Nếu bạn thích số nguyên VND (không có thập phân):
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->unsignedBigInteger('total_amount')->default(0)->after('cart_key'); 
            }
            // 2) Hoặc nếu muốn lưu số thập phân: dùng dòng dưới thay cho dòng trên
            // $table->decimal('total_amount', 12, 2)->default(0)->after('cart_key');

            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 20)->nullable()->after('total_amount'); // ví dụ: VNPAY, COD
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 20)->default('pending')->after('payment_method'); // pending|paid|failed
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status', 20)->default('pending')->after('payment_status'); // pending|processing|completed|cancelled
            }
            if (!Schema::hasColumn('orders', 'code')) {
                $table->string('code', 50)->unique()->after('status');
            }
            // Đã thêm ở bước trước:
            if (!Schema::hasColumn('orders', 'cart_key')) {
                $table->string('cart_key', 64)->nullable()->after('user_id');
                $table->index('cart_key', 'orders_cart_key_index');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) return;

        Schema::table('orders', function (Blueprint $table) {
            // Xoá các cột mới thêm nếu muốn (tuỳ bạn có dữ liệu thật chưa)
            if (Schema::hasColumn('orders', 'code')) $table->dropUnique('orders_code_unique');
            if (Schema::hasColumn('orders', 'cart_key')) $table->dropIndex('orders_cart_key_index');

            foreach (['total_amount','payment_method','payment_status','status','code','cart_key'] as $col) {
                if (Schema::hasColumn('orders', $col)) $table->dropColumn($col);
            }
            // Không ép nullable(user_id) quay lại NOT NULL để tránh lỗi dữ liệu
        });
    }
};
