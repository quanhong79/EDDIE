<?php

// database/migrations/2025_09_28_000001_add_options_to_carts.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'options')) {
                $table->json('options')->nullable()->after('quantity');
            }
        });
    }
    public function down(): void {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'options')) {
                $table->dropColumn('options');
            }
        });
    }
};
