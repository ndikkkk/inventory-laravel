<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            
            // 1. Identitas Barang
            $table->string('nama_barang');
            
            // 2. Relasi ke Akun/Kategori (PENGGANTI CATEGORY_ID)
            // Pastikan tabel 'accounts' sudah dibuat sebelum 'items'
            // (Biasanya urutan timestamp file migration accounts harus lebih lama/kecil dari items)
            // Jika error foreign key, hapus 'constrained' dulu jadi: $table->unsignedBigInteger('account_id');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade'); 
            
            // 3. Satuan & Stok
            $table->string('satuan')->default('Pcs');
            $table->integer('stok_awal_2026')->default(0);
            $table->integer('stok_saat_ini')->default(0);

            // 4. Harga (INI YANG TADI HILANG)
            $table->decimal('harga_satuan', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};