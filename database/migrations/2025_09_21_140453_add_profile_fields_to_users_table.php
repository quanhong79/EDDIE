<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thông tin cá nhân
            if (!Schema::hasColumn('users', 'phone'))   $table->string('phone', 20)->nullable()->after('email');
            if (!Schema::hasColumn('users', 'address')) $table->string('address', 255)->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'district'))$table->string('district', 100)->nullable()->after('address');
            if (!Schema::hasColumn('users', 'city'))    $table->string('city', 100)->nullable()->after('district');
            if (!Schema::hasColumn('users', 'country')) $table->string('country', 2)->nullable()->default('VN')->after('city');

            // Cài đặt
            if (!Schema::hasColumn('users', 'language')) $table->string('language', 10)->nullable()->default('vi')->after('country');
            if (!Schema::hasColumn('users', 'notifications'))
                $table->json('notifications')->nullable()->after('language');

            // Phương thức thanh toán mặc định (để lưu lựa chọn: COD/online)
            if (!Schema::hasColumn('users', 'default_payment_method'))
                $table->string('default_payment_method', 20)->nullable()->default('COD')->after('notifications');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['phone','address','district','city','country','language','notifications','default_payment_method'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
