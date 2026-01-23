<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. CARI ACCOUNT ID BERDASARKAN NAMA KATEGORI (LEVEL 3)
        // Kita cari di tabel accounts yang namanya mirip dengan input di Excel
        $account = Account::where('nama_akun', $row['kategori_level_3'])
                          ->where('level', 3) // Pastikan dia Level 3 (Rincian)
                          ->first();

        // Jika salah ketik/tidak ketemu, kita bisa skip atau throw error
        if (!$account) {
            // Opsional: Lempar error biar user tahu baris mana yang salah
            throw new \Exception("Kategori '" . $row['kategori_level_3'] . "' tidak ditemukan di database!");
        }

        // 2. SIMPAN BARANG
        return new Item([
            'nama_barang'    => $row['nama_barang'],
            'account_id'     => $account->id, // Pakai ID yang ditemukan tadi
            'satuan'         => $row['satuan'],
            'stok_saat_ini'  => $row['stok_awal'],
            'stok_awal_2026' => $row['stok_awal'], // Asumsi import adalah stok awal
            'harga_satuan'   => $row['harga_satuan'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_barang'      => 'required',
            'kategori_level_3' => 'required', // Ini kolom baru di Excel
            'satuan'           => 'required',
            'stok_awal'        => 'required|numeric',
            'harga_satuan'     => 'required|numeric',
        ];
    }
}
