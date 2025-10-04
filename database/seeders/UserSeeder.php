<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        User::firstOrCreate(
            ['email' => '1@1'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );
        User::firstOrCreate(
            ['email' => '1@2'],
            ['name' => 'User', 'password' => Hash::make('password'), 'role' => 'user']
        );
    }
}
