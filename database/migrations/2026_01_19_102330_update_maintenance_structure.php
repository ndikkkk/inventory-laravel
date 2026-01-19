<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Ubah item_id jadi NULLABLE (Boleh kosong)
        // Kita pakai Raw Query biar aman tanpa install plugin tambahan
        DB::statement("ALTER TABLE outgoing_transactions MODIFY item_id BIGINT UNSIGNED NULL");

        // 2. Tambah kolom deskripsi untuk nama jasa
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->after('item_id');
        });
    }

    public function down()
    {
        // Kembalikan ke semula (Not Null) - Hati-hati kalau ada data null
        // DB::statement("ALTER TABLE outgoing_transactions MODIFY item_id BIGINT UNSIGNED NOT NULL");
        Schema::table('outgoing_transactions', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};