<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasTable('orders')) return;
    Schema::table('orders', function (Blueprint $table) {
      if (Schema::hasColumn('orders', 'user_id')) {
        $table->unsignedBigInteger('user_id')->nullable()->change(); // cho phép guest
      }
      if (!Schema::hasColumn('orders', 'cart_key')) {
        $table->string('cart_key', 64)->nullable()->after('user_id');
        $table->index('cart_key', 'orders_cart_key_index');
      }
    });
  }
  public function down(): void {
    if (!Schema::hasTable('orders')) return;
    Schema::table('orders', function (Blueprint $table) {
      if (Schema::hasColumn('orders', 'cart_key')) {
        $table->dropIndex('orders_cart_key_index');
        $table->dropColumn('cart_key');
      }
    });
  }
};
