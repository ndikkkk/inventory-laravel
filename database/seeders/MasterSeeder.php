<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Account; // Kita pakai Model Account (bukan Category lagi)
use App\Models\Division;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA DIVISI (DIPERTAHANKAN)
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

        // ==========================================
        // 2. DATA AKUN BELANJA BERTINGKAT (BARU)
        // ==========================================

        Schema::disableForeignKeyConstraints();
        Account::truncate(); // Kosongkan tabel accounts
        Schema::enableForeignKeyConstraints();

        // Data Hierarki Sesuai Standar Pemerintah
        $dataAkun = [
            'Barang Pakai Habis' => [
                'Bahan' => [
                    'Bahan Bangunan dan Konstruksi',
                    'Bahan Kimia',
                    'Bahan Bakar dan Pelumas',
                    'Bahan Baku',
                    'Bahan Kimia Nuklir',
                    'Barang Dalam Proses',
                    'Bahan/Bibit Tanaman',
                    'Isi Tabung Pemadam Kebakaran',
                    'Isi Tabung Gas',
                    'Bahan/Bibit Ternak/Bibit Ikan',
                    'Bahan Lainnya',
                ],
                'Suku Cadang' => [
                    'Suku Cadang Alat Angkutan',
                    'Suku Cadang Alat Besar',
                    'Suku Cadang Alat Kedokteran',
                    'Suku Cadang Alat Laboratorium',
                    'Suku Cadang Alat Pemancar',
                    'Suku Cadang Alat Studio dan Komunikasi',
                    'Suku Cadang Alat Pertanian',
                    'Suku Cadang Alat Bengkel',
                    'Persediaan dari Belanja Bantuan Sosial',
                    'Suku Cadang Lainnya',
                ],
                'Alat/Bahan Untuk Kegiatan Kantor' => [
                    'Alat Tulis Kantor',
                    'Kertas dan Cover',
                    'Bahan Cetak',
                    'Benda Pos',
                    'Persediaan Dokumen/Administrasi Tender',
                    'Bahan Komputer',
                    'Perabot Kantor',
                    'Alat Listrik',
                    'Perlengkapan Dinas',
                    'Kaporlap dan Perlengkapan Satwa',
                    'Perlengkapan Pendukung Olahraga',
                    'Suvenir/Cendera Mata',
                    'Alat/Bahan untuk Kegiatan Kantor Lainnya',
                ],
                'Obat-obatan' => [
                    'Obat',
                    'Obat-obatan Lainnya',
                ],
                'Persediaan untuk Dijual/Diserahkan' => [
                    'Persediaan untuk Dijual/Diserahkan Kepada Masyarakat',
                    'Persediaan Untuk Dijual/Diserahkan Lainnya',
                ],
                'Natura dan Pakan' => [
                    'Natura',
                    'Pakan',
                    'Natura dan Pakan Lainnya',
                ],
            ],
            'Barang Tak Habis Pakai' => [
                'Komponen' => [
                    'Komponen Jembatan Baja',
                    'Komponen Jembatan Pratekan',
                    'Komponen Peralatan',
                    'Komponen Rambu-Rambu',
                    'Attachment',
                    'Komponen Lainnya',
                ],
                'Pipa' => [
                    'Pipa Air Besi Tuang (DCI)',
                    'Pipa Asbes Semen (ACP)',
                    'Pipa Baja',
                    'Pipa Beton Pratekan',
                    'Pipa Fiber Glass',
                    'Pipa Plastik PVC (UPVC)',
                    'Pipa Lainnya',
                ],
            ],
            'Barang Bekas Dipakai' => [
                'Komponen Bekas dan Pipa Bekas' => [
                    'Komponen Bekas',
                    'Pipa Bekas',
                    'Komponen Bekas dan Pipa Bekas Lainnya',
                ],
            ],
        ];

        // LOGIKA SIMPAN KE DATABASE
        foreach ($dataAkun as $level1 => $kelompok) {
            // Level 1: Jenis
            $parent1 = Account::create(['nama_akun' => $level1, 'level' => 1]);

            foreach ($kelompok as $level2 => $objek) {
                // Level 2: Kelompok
                $parent2 = Account::create(['nama_akun' => $level2, 'parent_id' => $parent1->id, 'level' => 2]);

                foreach ($objek as $level3) {
                    // Level 3: Rincian Objek
                    Account::create(['nama_akun' => $level3, 'parent_id' => $parent2->id, 'level' => 3]);
                }
            }
        }
    }
}
