<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatatanObatToHasilperiksaObatTable extends Migration
{
    public function up()
    {
        Schema::table('hasilperiksa_obat', function (Blueprint $table) {
            $table->text('catatan_obat')->nullable()->after('jumlah');
        });
    }

    public function down()
    {
        Schema::table('hasilperiksa_obat', function (Blueprint $table) {
            $table->dropColumn('catatan_obat');
        });
    }
}
