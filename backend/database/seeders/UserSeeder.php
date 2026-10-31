<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin (hanya bisa dibuat langsung di sistem/seeder, dapat mengelola admin)
        User::updateOrCreate(
            ['email' => 'superadmin@masjid-bgc.com'],
            [
                'name' => 'Super Administrator BGC',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '081234567890',
                'address' => 'Jl. Bukit Gading Cisoka Blok A No. 1',
                'is_active' => true,
            ]
        );

        // 2. Admin Masjid (pengurus yang mengelola operasional)
        User::updateOrCreate(
            ['email' => 'admin@masjid-bgc.com'],
            [
                'name' => 'H. Ahmad Dahlan (Admin Masjid)',
                'password' => Hash::make('password'),
                'role' => 'admin_masjid',
                'phone' => '081234567891',
                'address' => 'Perum Bukit Gading Cisoka Blok B No. 12',
                'is_active' => true,
            ]
        );

        // 3. Marbot (dibuatkan akun oleh Admin Masjid, diberi gelar/peran marbot)
        User::updateOrCreate(
            ['email' => 'marbot@masjid-bgc.com'],
            [
                'name' => 'Pak Joko Sutrisno (Marbot)',
                'password' => Hash::make('password'),
                'role' => 'marbot',
                'phone' => '081234567892',
                'address' => 'Komplek Masjid BGC Cisoka',
                'is_active' => true,
            ]
        );

        // 4. Jamaah (jamaah terdaftar)
        User::updateOrCreate(
            ['email' => 'jamaah@masjid-bgc.com'],
            [
                'name' => 'Fadhil Bima (Jamaah)',
                'password' => Hash::make('password'),
                'role' => 'jamaah',
                'phone' => '081234567893',
                'address' => 'Perum Bukit Gading Cisoka Blok D No. 5',
                'is_active' => true,
            ]
        );
    }
}
