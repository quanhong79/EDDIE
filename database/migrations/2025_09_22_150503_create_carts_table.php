<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();         // user login
                $table->string('cart_key', 64)->nullable()->index();       // guest key (cookie)
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->decimal('price', 12, 2)->nullable();               // snapshot tại thời điểm thêm
                $table->unsignedInteger('quantity')->default(1);

                // biến thể
                $table->string('selected_color')->nullable();
                $table->string('selected_size')->nullable();

                $table->timestamps();

                // index gộp để gộp dòng cùng biến thể
                $table->index(['user_id', 'product_id', 'selected_color', 'selected_size'], 'carts_user_variant_idx');
                $table->index(['cart_key', 'product_id', 'selected_color', 'selected_size'], 'carts_guest_variant_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
