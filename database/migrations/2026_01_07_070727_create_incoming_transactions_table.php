<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incoming_transactions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Barang
            // onDelete('cascade') artinya jika barang dihapus, riwayat masuknya juga terhapus
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            
            $table->date('tanggal');
            $table->integer('jumlah');
            
            // --- INI YANG KITA TAMBAHKAN AGAR DASHBOARD TIDAK ERROR ---
            // Menyimpan harga beli saat transaksi terjadi
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            
            // Snapshot sisa stok setelah barang masuk (penting untuk laporan mutasi)
            $table->integer('sisa_stok')->nullable();

            $table->string('supplier')->nullable(); // Opsional
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_transactions');
    }
};