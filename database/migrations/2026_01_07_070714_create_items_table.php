<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('nama_barang');
        $table->string('satuan'); // Pcs, Rim, Box
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->integer('stok_awal_2026')->default(0); // Stok per 31 Des 2025
        $table->integer('stok_saat_ini')->default(0); // Stok real-time
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
