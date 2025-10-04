<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private function indexExists(string $table, string $index): bool
    {
        $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($rows);
    }

    public function up(): void
    {
        if (!Schema::hasTable('carts')) return;

        // 1) Thêm cột nếu chưa có
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'selected_color')) {
                $table->string('selected_color')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('carts', 'selected_size')) {
                $table->string('selected_size')->nullable()->after('selected_color');
            }
        });

        // 2) Thêm index gộp, chỉ tạo nếu chưa tồn tại
        if (!$this->indexExists('carts', 'carts_user_variant_idx')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->index(
                    ['user_id', 'product_id', 'selected_color', 'selected_size'],
                    'carts_user_variant_idx'
                );
            });
        }
        if (!$this->indexExists('carts', 'carts_guest_variant_idx')) {
            Schema::table('carts', function (Blueprint $table) {
                // dùng cho guest lưu theo cart_key
                if (Schema::hasColumn('carts', 'cart_key')) {
                    $table->index(
                        ['cart_key', 'product_id', 'selected_color', 'selected_size'],
                        'carts_guest_variant_idx'
                    );
                }
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('carts')) return;

        // Xóa index trước (nếu cần)
        if ($this->indexExists('carts', 'carts_user_variant_idx')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex('carts_user_variant_idx');
            });
        }
        if ($this->indexExists('carts', 'carts_guest_variant_idx')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex('carts_guest_variant_idx');
            });
        }

        // Xóa cột (an toàn nếu tồn tại)
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'selected_size'))  $table->dropColumn('selected_size');
            if (Schema::hasColumn('carts', 'selected_color')) $table->dropColumn('selected_color');
        });
    }
};
