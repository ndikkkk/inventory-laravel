<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingTransaction extends Model
{
    use HasFactory;

    protected $table = 'outgoing_transactions'; // Memastikan nama tabel benar

    // === INI BIANG KEROKNYA ===
    // Semua kolom ini WAJIB ada disini supaya bisa tersimpan ke database
    protected $fillable = [
        'item_id',       // ID Barang (Bisa NULL untuk servis)
        'division_id',   // ID Bidang
        'user_id',       // ID Admin yang input (Opsional)
        'tanggal',       // Tanggal Transaksi
        
        'jumlah',        // <--- INI YANG TADI KOSONG
        'status',        // <--- INI JUGA
        
        // Kolom Tambahan (Pemeliharaan)
        'deskripsi',     
        'km_saat_ini',   
        'km_berikutnya',
        
        // Kolom Keuangan
        'harga_satuan',
        'total_harga',
        'sisa_stok'
    ];

    // Relasi ke Barang
    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault([
            'nama_barang' => 'Jasa / Servis (Non-Barang)',
            'satuan' => '-'
        ]);
    }

    // Relasi ke Bidang
    public function division()
    {
        return $this->belongsTo(Division::class)->withDefault([
            'nama_bidang' => 'Tanpa Divisi'
        ]);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}