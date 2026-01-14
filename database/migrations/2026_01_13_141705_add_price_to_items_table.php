<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('items', function (Blueprint $table) {
        // Kolom untuk menyimpan harga satuan terakhir dari Excel/Input
        $table->bigInteger('harga_satuan')->default(0)->after('stok_saat_ini');
    });
}

public function down(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('harga_satuan');
    });
}
};