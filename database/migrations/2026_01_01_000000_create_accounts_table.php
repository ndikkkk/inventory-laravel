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
    Schema::create('accounts', function (Blueprint $table) {
        $table->id();
        $table->string('nama_akun'); // Nama (Contoh: Bahan Bangunan)
        $table->unsignedBigInteger('parent_id')->nullable(); // Induknya siapa
        $table->integer('level'); // 1, 2, atau 3
        $table->timestamps();

        // Relasi ke tabel ini sendiri (Self Join)
        $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};