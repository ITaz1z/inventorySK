<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Gudang Umum
        User::create([
            'name' => 'Eka',
            'email' => 'admin.gudang.umum@company.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_gudang_umum',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Admin Gudang Sparepart
        User::create([
            'name' => 'Damai',
            'email' => 'admin.gudang.sparepart@company.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_gudang_sparepart',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Purchasing 1
        User::create([
            'name' => 'Dhea',
            'email' => 'purchasing1@company.com',
            'password' => Hash::make('password123'),
            'role' => 'purchasing_1',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Purchasing 2
        User::create([
            'name' => 'Yozi',
            'email' => 'purchasing2@company.com',
            'password' => Hash::make('password123'),
            'role' => 'purchasing_2',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // General Manager
        User::create([
            'name' => 'General Manager',
            'email' => 'gm@company.com',
            'password' => Hash::make('password123'),
            'role' => 'general_manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Atasan
        User::create([
            'name' => 'Direktur',
            'email' => 'atasan@company.com',
            'password' => Hash::make('password123'),
            'role' => 'atasan',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
