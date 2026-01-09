<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom Role & Divisi di tabel Users
        Schema::table('users', function (Blueprint $table) {
            // Role: 'admin' atau 'bidang'
            $table->string('role')->default('bidang')->after('email'); 
            // Relasi ke divisi (Boleh null jika dia admin pusat)
            $table->foreignId('division_id')->nullable()->after('role');
        });

        // 2. Tambah kolom Status di tabel Barang Keluar
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            // Status: 'pending', 'approved', 'rejected'
            $table->string('status')->default('pending')->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'division_id']);
        });

        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};