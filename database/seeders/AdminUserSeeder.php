<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name'     => 'Admin User',
            'email'    => 'haxyz04@gmail.com',
            'password' => Hash::make('admin'), // Mật khẩu mặc định
            'role'     => 'admin',
        ]);
    }
}
