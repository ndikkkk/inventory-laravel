<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom di tabel Barang Masuk
        Schema::table('incoming_transactions', function (Blueprint $table) {
            $table->integer('sisa_stok')->nullable()->after('total_harga');
        });

        // Tambah kolom di tabel Barang Keluar
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->integer('sisa_stok')->nullable()->after('total_harga');
        });
    }

    public function down()
    {
        Schema::table('incoming_transactions', function (Blueprint $table) {
            $table->dropColumn('sisa_stok');
        });
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->dropColumn('sisa_stok');
        });
    }
};
