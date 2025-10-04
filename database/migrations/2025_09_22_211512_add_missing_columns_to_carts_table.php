<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                // Cho phép guest
                if (Schema::hasColumn('carts', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                } else {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }

                if (!Schema::hasColumn('carts', 'cart_key')) {
                    $table->string('cart_key', 64)->nullable()->after('user_id')->index();
                }

                // Sản phẩm + giá snapshot
                if (!Schema::hasColumn('carts', 'product_id')) {
                    $table->foreignId('product_id')->after('cart_key')->constrained()->cascadeOnDelete();
                }

                if (!Schema::hasColumn('carts', 'price')) {
                    $table->decimal('price', 12, 2)->nullable()->after('product_id');
                }

                if (!Schema::hasColumn('carts', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1)->after('price');
                }

                // Biến thể
                if (!Schema::hasColumn('carts', 'selected_color')) {
                    $table->string('selected_color')->nullable()->after('quantity');
                }
                if (!Schema::hasColumn('carts', 'selected_size')) {
                    $table->string('selected_size')->nullable()->after('selected_color');
                }

                // Index gộp cho hợp nhất dòng biến thể
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_map(fn($idx) => $idx->getName(), $sm->listTableIndexes('carts'));

                if (!in_array('carts_user_variant_idx', $indexes)) {
                    $table->index(['user_id','product_id','selected_color','selected_size'], 'carts_user_variant_idx');
                }
                if (!in_array('carts_guest_variant_idx', $indexes)) {
                    $table->index(['cart_key','product_id','selected_color','selected_size'], 'carts_guest_variant_idx');
                }
            });
        } else {
            // Nếu vì lý do gì đó bảng chưa tồn tại, tạo mới đầy đủ
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('cart_key', 64)->nullable()->index();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->decimal('price', 12, 2)->nullable();
                $table->unsignedInteger('quantity')->default(1);
                $table->string('selected_color')->nullable();
                $table->string('selected_size')->nullable();
                $table->timestamps();

                $table->index(['user_id','product_id','selected_color','selected_size'], 'carts_user_variant_idx');
                $table->index(['cart_key','product_id','selected_color','selected_size'], 'carts_guest_variant_idx');
            });
        }
    }

    public function down(): void
    {
        // Chỉ rollback các cột thêm vào (an toàn)
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (Schema::hasColumn('carts', 'price')) $table->dropColumn('price');
                if (Schema::hasColumn('carts', 'selected_color')) $table->dropColumn('selected_color');
                if (Schema::hasColumn('carts', 'selected_size')) $table->dropColumn('selected_size');
                if (Schema::hasColumn('carts', 'cart_key')) $table->dropIndex(['cart_key']); // Laravel sẽ tự map tên index
            });
        }
    }
};

