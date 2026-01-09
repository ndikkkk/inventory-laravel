<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
use HasFactory;
protected $guarded = ['id'];

// Relasi: Barang milik satu Kategori
public function category()
{
return $this->belongsTo(Category::class);
}
}