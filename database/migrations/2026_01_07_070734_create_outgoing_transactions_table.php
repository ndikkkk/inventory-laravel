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
        Schema::create('outgoing_transactions', function (Blueprint $table) {
            $table->id();

            // 1. Relasi ke Barang (Boleh Kosong/Nullable untuk Jasa Servis)
            // onDelete('set null') artinya jika barang dihapus, riwayatnya tetap ada tapi item_id jadi null
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');

            // 2. Deskripsi (Wajib isi jika item_id kosong, misal: "Ganti Oli")
            $table->string('deskripsi')->nullable();

            // 3. Relasi ke Bidang (Siapa yang ambil)
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');

            // 4. Siapa Admin yang input (Opsional)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // 5. Data Transaksi Dasar
            $table->date('tanggal');
            $table->integer('jumlah');
            
            // 6. Status Persetujuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // 7. Data Keuangan (Penting untuk Laporan Aset)
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            // 8. Snapshot Sisa Stok (Penting untuk Laporan Mutasi)
            // Mencatat sisa stok *saat itu* setelah barang diambil
            $table->integer('sisa_stok')->nullable();

            // 9. Khusus Pemeliharaan Kendaraan
            $table->integer('km_saat_ini')->nullable();
            $table->integer('km_berikutnya')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_transactions');
    }
};