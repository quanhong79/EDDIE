<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('products')) return;

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'size_mode')) {
                // none | apparel | shoes
                $table->string('size_mode', 20)->default('none')->after('quantity');
            }
            if (!Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable()->after('size_mode');
            }
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->json('sizes')->nullable()->after('colors');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) return;

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sizes'))     $table->dropColumn('sizes');
            if (Schema::hasColumn('products', 'colors'))    $table->dropColumn('colors');
            if (Schema::hasColumn('products', 'size_mode')) $table->dropColumn('size_mode');
        });
    }
};
