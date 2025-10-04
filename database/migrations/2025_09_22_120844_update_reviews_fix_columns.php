<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('reviews', function (Blueprint $table) {
        // Đổi tên comment -> content
        if (Schema::hasColumn('reviews', 'comment')) {
            $table->renameColumn('comment', 'content');
        }

        // Xoá approved nếu đã có status
        if (Schema::hasColumn('reviews', 'approved')) {
            $table->dropColumn('approved');
        }
    });
}

public function down()
{
    Schema::table('reviews', function (Blueprint $table) {
        if (Schema::hasColumn('reviews', 'content')) {
            $table->renameColumn('content', 'comment');
        }
        if (!Schema::hasColumn('reviews', 'approved')) {
            $table->boolean('approved')->default(false);
        }
    });
}

};
