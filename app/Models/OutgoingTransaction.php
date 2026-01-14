<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'division_id',
        'tanggal',
        'jumlah',
        'status', // <-- Tambahan penting!
        'harga_satuan',
        'total_harga',
        'sisa_stok'
    ];

    // Relasi ke Barang
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relasi ke Divisi (Bidang)
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
