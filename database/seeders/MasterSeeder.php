<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Wajib import ini
use App\Models\Category;
use App\Models\Division;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. DATA KATEGORI (BERSIHKAN DULU, LALU ISI BARU)
        // ==========================================

        // Matikan pengecekan kunci asing sementara agar bisa menghapus paksa
        Schema::disableForeignKeyConstraints();

        // HAPUS SEMUA DATA KATEGORI LAMA (Reset ID ke 1)
        Category::truncate();

        // Hidupkan kembali pengecekan
        Schema::enableForeignKeyConstraints();

        // Daftar 11 Kategori Baru
        $categories = [
            'Alat Tulis Kantor',
            'Kertas dan Cover',
            'Bahan Cetak',
            'Benda Pos',
            'Persediaan Dokumen/Administrasi Tender',
            'Bahan Komputer',
            'Perabot Kantor',
            'Alat Listrik',
            'Perlengkapan Pendukung Olahraga',
            'Souvenir/Cendera Mata',
            'Alat/Bahan untuk Kegiatan Kantor Lainnya',
        ];

        foreach ($categories as $namaKategori) {
            Category::create(['nama_kategori' => $namaKategori]);
        }

        // ==========================================
        // 2. DATA DIVISI (UPDATE/TAMBAH)
        // ==========================================
        $divisions = [
            ['id' => 1, 'nama_bidang' => 'Sekretariat'],
            ['id' => 2, 'nama_bidang' => 'PSDM (Pengembangan Sumber Daya Manusia)'],
            ['id' => 3, 'nama_bidang' => 'PM (Pengadaan dan Mutasi)'],
            ['id' => 4, 'nama_bidang' => 'KAI (Kinerja Aparatur dan Informasi)'],
        ];

        foreach ($divisions as $div) {
            Division::updateOrCreate(
                ['id' => $div['id']],
                ['nama_bidang' => $div['nama_bidang']]
            );
        }
    }
}
