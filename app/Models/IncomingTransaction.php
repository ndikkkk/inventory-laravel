<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingTransaction extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi kecuali ID
    protected $guarded = ['id'];

    // Relasi: Transaksi ini milik satu Barang (Item)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}