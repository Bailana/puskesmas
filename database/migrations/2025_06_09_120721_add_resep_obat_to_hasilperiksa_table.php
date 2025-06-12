<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResepObatToHasilperiksaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hasilperiksa', function (Blueprint $table) {
            $table->unsignedBigInteger('resep_obat')->nullable()->after('kesan_status_gizi');
            $table->foreign('resep_obat')->references('id')->on('obat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasilperiksa', function (Blueprint $table) {
            $table->dropForeign(['resep_obat']);
            $table->dropColumn('resep_obat');
        });
    }
}
