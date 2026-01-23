<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    // === TAMBAHKAN INI (HUBUNGAN KE INDUK) ===
    public function parent()
    {
        // Setiap akun milik satu parent (belongsTo dirinya sendiri)
        return $this->belongsTo(Account::class, 'parent_id');
    }

    // === TAMBAHKAN INI JUGA (OPSIONAL, UNTUK MASA DEPAN) ===
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
