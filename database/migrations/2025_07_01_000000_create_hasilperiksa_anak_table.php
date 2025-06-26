<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilperiksaAnakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasilperiksa_anak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pasien_id');
            $table->string('berat_badan')->nullable();
            $table->text('makanan_anak')->nullable();
            $table->text('gejala')->nullable();
            $table->text('nasehat')->nullable();
            $table->text('pegobatan')->nullable();
            $table->unsignedBigInteger('penanggung_jawab')->nullable();
            $table->timestamps();

            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
            $table->foreign('penanggung_jawab')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasilperiksa_anak');
    }
}
