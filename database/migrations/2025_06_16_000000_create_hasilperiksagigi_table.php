<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilperiksagigiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasilperiksagigi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('penanggung_jawab');
            $table->date('tanggal_periksa');
            $table->text('odontogram')->nullable();
            $table->text('pemeriksaan_subjektif')->nullable();
            $table->text('pemeriksaan_objektif')->nullable();
            $table->text('diagnosa')->nullable();
            $table->text('terapi_anjuran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
            $table->foreign('penanggung_jawab')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasilperiksagigi');
    }
}
