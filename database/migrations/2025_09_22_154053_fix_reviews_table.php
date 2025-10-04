<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Nếu chưa có cột thì thêm
            if (!Schema::hasColumn('reviews', 'comment')) {
                $table->text('comment')->nullable()->after('rating');
            }

            if (!Schema::hasColumn('reviews', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('comment');
            }

            // Unique để tránh user review nhiều lần cùng 1 product
            $table->unique(['product_id', 'user_id'], 'reviews_product_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('reviews', 'comment')) {
                $table->dropColumn('comment');
            }
            $table->dropUnique('reviews_product_user_unique');
        });
    }
};
