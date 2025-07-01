<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPenanggungJawabToHasilanalisaRawatinapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hasilanalisa_rawatinap', function (Blueprint $table) {
            $table->string('penanggung_jawab')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasilanalisa_rawatinap', function (Blueprint $table) {
            $table->dropColumn('penanggung_jawab');
        });
    }
}
