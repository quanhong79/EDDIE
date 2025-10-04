<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function indexExists(string $table, string $name): bool {
        try {
            $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]);
            return !empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function up(): void
    {
        if (!Schema::hasTable('carts')) return;

        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'cart_key')) {
                // khoá nhận diện giỏ của guest (đặt trong cookie), có thể là uuid
                $table->string('cart_key', 64)->nullable()->after('user_id');
            }
        });

        // index cho path guest
        if ($this->indexExists('carts', 'carts_guest_variant_idx') === false) {
            Schema::table('carts', function (Blueprint $table) {
                $table->index(['cart_key', 'product_id', 'selected_color', 'selected_size'], 'carts_guest_variant_idx');
            });
        }

        // (không bắt buộc) index cho path user
        if ($this->indexExists('carts', 'carts_user_variant_idx') === false) {
            Schema::table('carts', function (Blueprint $table) {
                $table->index(['user_id', 'product_id', 'selected_color', 'selected_size'], 'carts_user_variant_idx');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('carts')) return;

        if ($this->indexExists('carts', 'carts_guest_variant_idx')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex('carts_guest_variant_idx');
            });
        }
        // Không bắt buộc drop index user

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'cart_key')) {
                $table->dropColumn('cart_key');
            }
        });
    }
};
