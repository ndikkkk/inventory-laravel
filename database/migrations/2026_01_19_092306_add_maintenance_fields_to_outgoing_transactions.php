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
    Schema::table('outgoing_transactions', function (Blueprint $table) {
        // Kolom khusus pemeliharaan (Boleh NULL karena transaksi ATK biasa tidak pakai ini)
        $table->string('nopol_kendaraan')->nullable()->after('division_id'); // Plat Nomor (Opsional)
        $table->integer('km_saat_ini')->nullable()->after('nopol_kendaraan');
        $table->integer('km_berikutnya')->nullable()->after('km_saat_ini');
    });
}

public function down()
{
    Schema::table('outgoing_transactions', function (Blueprint $table) {
        $table->dropColumn(['nopol_kendaraan', 'km_saat_ini', 'km_berikutnya']);
    });
}
};