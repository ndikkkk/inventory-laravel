<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari Kategori berdasarkan nama di Excel
        // Jika tidak ada, otomatis buat baru (firstOrCreate)
        $category = Category::firstOrCreate([
            'nama_kategori' => $row['kategori']
        ]);

        // 2. Masukkan Data Barang
        return new Item([
            'nama_barang'    => $row['nama_barang'],
            'category_id'    => $category->id, // Pakai ID dari kategori yg ditemukan/dibuat
            'satuan'         => $row['satuan'],
            'stok_awal_2026' => $row['stok_awal'],
            'stok_saat_ini'  => $row['stok_awal'], // Stok awal = Stok saat ini
        ]);
    }
}