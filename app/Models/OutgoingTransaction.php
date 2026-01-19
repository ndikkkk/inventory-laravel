<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',       // Sekarang boleh NULL (untuk servis)
        'deskripsi',     // <--- TAMBAHAN: Nama Jasa/Servis
        'division_id',
    //    'user_id',       // <--- TAMBAHAN: Siapa yang input
        'tanggal',
        'jumlah',
        'status', 
        'km_saat_ini',   // <--- TAMBAHAN: Kilometer
        'km_berikutnya', // <--- TAMBAHAN: Kilometer
        'harga_satuan',
        'total_harga',
        'sisa_stok'
    ];

    // Relasi ke Barang
    public function item()
    {
        // withDefault() PENTING! 
        // Agar jika item_id NULL (transaksi servis), kode tidak error saat panggil $t->item->nama_barang
        return $this->belongsTo(Item::class)->withDefault([
            'nama_barang' => null,
            'satuan' => '-'
        ]);
    }

    // Relasi ke Divisi (Bidang)
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Relasi ke User (Opsional, biar tahu siapa yang input)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}