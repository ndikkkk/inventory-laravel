<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom harga di Transaksi Masuk (Pemasukan)
        Schema::table('incoming_transactions', function (Blueprint $table) {
            // Menggunakan BigInteger agar cukup menampung angka Rupiah yang besar
            $table->bigInteger('harga_satuan')->default(0)->after('jumlah'); 
            $table->bigInteger('total_harga')->default(0)->after('harga_satuan');
        });

        // 2. Tambah kolom harga di Transaksi Keluar (Pengeluaran Aset)
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->bigInteger('harga_satuan')->default(0)->after('jumlah');
            $table->bigInteger('total_harga')->default(0)->after('harga_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('incoming_transactions', function (Blueprint $table) {
            $table->dropColumn(['harga_satuan', 'total_harga']);
        });
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->dropColumn(['harga_satuan', 'total_harga']);
        });
    }
};