<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            }

            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug', 128)->nullable()->after('name')->unique();
            }
        });

        // Tạo slug cho dữ liệu cũ
        DB::transaction(function () {
            $rows = DB::table('categories')->select('id','name','slug')->get();
            $used = DB::table('categories')->whereNotNull('slug')->pluck('slug')->all();
            $used = array_flip($used);

            foreach ($rows as $r) {
                if (!empty($r->slug)) continue;

                $base = $r->name ? Str::slug($r->name) : 'category-'.$r->id;
                $slug = $base;
                $i = 2;
                while (isset($used[$slug])) {
                    $slug = $base.'-'.$i++;
                }
                $used[$slug] = true;

                DB::table('categories')->where('id',$r->id)->update(['slug'=>$slug]);
            }
        });
    }

    public function down(): void {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories','slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('categories','parent_id')) {
                try { $table->dropForeign(['parent_id']); } catch (\Throwable $e) {}
                $table->dropColumn('parent_id');
            }
        });
    }
};

