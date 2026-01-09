<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan/Hapus baris ini jika ada (JANGAN DIPAKAI):
        // \App\Models\User::factory(10)->create();

        // 2. Pastikan urutan panggilannya seperti ini:
        $this->call([
            UserSeeder::class,    // Buat Admin BKPSDM (yg ada NIP-nya)
            MasterSeeder::class,  // Buat Kategori & Divisi
            // Seeder lain jika ada...
        ]);
    }
}