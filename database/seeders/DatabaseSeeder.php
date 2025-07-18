<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Hapus atau beri komentar pada factory jika tidak digunakan
        // \App\Models\User::factory(10)->create();

        // Panggil seeder yang ingin dijalankan
        $this->call([
            AdminUserSeeder::class,
            // Anda bisa menambahkan seeder lain di sini di masa depan
            // BookSeeder::class,
        ]);
    }
}
