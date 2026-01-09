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
    Schema::table('users', function (Blueprint $table) {
        // NIP biasanya 18 digit, kita taruh setelah ID
        // Kita set unique agar tidak ada NIP ganda
        $table->string('nip', 20)->unique()->after('id'); 
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('nip');
    });
}
};