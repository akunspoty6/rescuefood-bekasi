<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pelanggan (Customer)
        User::updateOrCreate(
            ['email' => 'dimas@penyelamat.id'],
            [
                'name' => 'Dimas Pratama (Mahasiswa Unisma)',
                'phone' => '081234567890',
                'role' => 'customer',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Akun Kasir Mitra (Merchant)
        User::updateOrCreate(
            ['email' => 'kasir@holland.id'],
            [
                'name' => 'Kasir Holland Bakery Summarecon',
                'phone' => '081288991122',
                'role' => 'merchant',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Akun Pengawas Lingkungan (DKPP)
        User::updateOrCreate(
            ['email' => 'officer@dkpp.bekasi.go.id'],
            [
                'name' => 'Ir. Bambang Sutrisno (Pengawas DKPP)',
                'phone' => '081199887766',
                'role' => 'dkpp',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
