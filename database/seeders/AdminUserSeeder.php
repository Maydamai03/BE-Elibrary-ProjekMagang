<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat user baru dengan data spesifik
        User::create([
            'nama_lengkap' => 'Admin 1',
            'email' => 'admin@elibrary.com',
            'password' => Hash::make('12345678'), // Ganti dengan password yang kuat
            'role' => 'admin',
            'email_verified_at' => now(), // Opsional: langsung verifikasi email
        ]);
    }
}
