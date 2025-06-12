<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilperiksaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasilperiksa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('penanggung_jawab');
            $table->date('tanggal_periksa');
            $table->text('anamnesis')->nullable();
            $table->text('pemeriksaan_fisik')->nullable();
            $table->text('rencana_dan_terapi')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('edukasi')->nullable();
            $table->string('kode_icd')->nullable();
            $table->enum('kesan_status_gizi', ['Gizi Kurang/Buruk', 'Gizi Cukup', 'Gizi Lebih'])->nullable();
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
        Schema::dropIfExists('hasilperiksa');
    }
}
