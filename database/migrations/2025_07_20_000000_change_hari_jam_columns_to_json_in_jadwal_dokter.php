<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeHariJamColumnsToJsonInJadwalDokter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_dokter', function (Blueprint $table) {
            // Change columns to TEXT type to store JSON strings without strict validation
            $table->text('hari')->nullable()->change();
            $table->text('jam_masuk')->nullable()->change();
            $table->text('jam_keluar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwal_dokter', function (Blueprint $table) {
            // Revert columns back to original types
            $table->string('hari')->nullable()->change();
            $table->time('jam_masuk')->nullable()->change();
            $table->time('jam_keluar')->nullable()->change();
        });
    }
}
