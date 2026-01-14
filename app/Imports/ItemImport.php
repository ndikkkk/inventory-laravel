<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // 1. Import Validasi

// 2. Tambahkan implements WithValidation
class ItemImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil data (Logika Insert Masih Sama)
            $stok  = isset($row['stok_saat_ini']) ? (int) $row['stok_saat_ini'] : 0;
            $harga = isset($row['harga_satuan']) ? (float) $row['harga_satuan'] : 0;

            // Karena sudah divalidasi, kita yakin kategori_id pasti ada di DB
            $kategoriId = $row['kategori_id'];

            Item::create([
                'nama_barang'    => $row['nama_barang'],
                'category_id'    => $kategoriId,
                'satuan'         => $row['satuan'] ?? 'Pcs',
                'harga_satuan'   => $harga,
                'stok_awal_2026' => $stok,
                'stok_saat_ini'  => $stok,
            ]);
        }
    }

    // 3. ATURAN VALIDASI (Agar Pinter)
    public function rules(): array
    {
        return [
            // Kolom 'nama_barang' wajib diisi
            'nama_barang' => 'required',

            // Kolom 'kategori_id' wajib angka & WAJIB ADA DI TABEL categories KOLOM ID
            // Inilah yang mencegah error SQL Integrity tadi!
            'kategori_id' => 'required|integer|exists:categories,id',
        ];
    }

    // 4. PESAN ERROR KUSTOM (Biar enak dibaca manusia)
    public function customValidationMessages()
    {
        return [
            'kategori_id.exists' => 'ID Kategori tidak ditemukan di database. Pastikan ID 1-11 saja.',
            'kategori_id.required' => 'Kolom kategori_id tidak boleh kosong.',
            'nama_barang.required' => 'Nama Barang tidak boleh kosong.',
        ];
    }
}
