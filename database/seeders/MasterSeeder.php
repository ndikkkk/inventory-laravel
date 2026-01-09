<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Tambahkan ini buat jaga-jaga
use App\Models\Category;
use App\Models\Division;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DATA KATEGORI (Biarkan seperti semula)
        // Kita gunakan upsert/create agar tidak error kalau dijalankan ulang
        $categories = [
            ['nama_kategori' => 'Kertas & Amplop'],
            ['nama_kategori' => 'Alat Tulis (Pena/Pensil)'],
            ['nama_kategori' => 'Binder & Map'],
            ['nama_kategori' => 'Peralatan Meja (Stapler/Pelubang)'],
        ];

        foreach ($categories as $cat) {
            // Cek dulu biar gak dobel
            if (Category::where('nama_kategori', $cat['nama_kategori'])->doesntExist()) {
                Category::create($cat);
            }
        }

        // 2. DATA DIVISI (INI YANG KITA UBAH SESUAI REQUEST)
        // Sekretariat, PSDM, KM, KAI
        $divisions = [
            ['id' => 1, 'nama_bidang' => 'Sekretariat'], // ID 1 biasanya untuk Admin/Sekretariat
            ['id' => 2, 'nama_bidang' => 'PSDM (Pengembangan Sumber Daya Manuasia'],
            ['id' => 3, 'nama_bidang' => 'PM (Pengadaan dan Mutasi)'],
            ['id' => 4, 'nama_bidang' => 'KAI (Kinerja Aparatur dan Informasi) '],
        ];

        foreach ($divisions as $div) {
            // Kita pakai updateOrCreate.
            // Artinya: Jika ID 1 sudah ada, update namanya. Jika belum, buat baru.
            Division::updateOrCreate(
                ['id' => $div['id']],
                ['nama_bidang' => $div['nama_bidang']]
            );
        }
    }
}