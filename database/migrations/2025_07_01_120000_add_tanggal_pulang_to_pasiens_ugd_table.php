<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalPulangToPasiensUgdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasiens_ugd', function (Blueprint $table) {
            $table->dateTime('tanggal_pulang')->nullable()->after('tanggal_masuk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pasiens_ugd', function (Blueprint $table) {
            $table->dropColumn('tanggal_pulang');
        });
    }
}
