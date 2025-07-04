<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJadwalDokterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_dokter', function (Blueprint $table) {
            // Drop old day columns if they exist
            $columnsToDrop = [
                'senin',
                'selasa',
                'rabu',
                'kamis',
                'jumat',
                'sabtu',
                'minggu',
                'senin_masuk',
                'senin_keluar',
                'selasa_masuk',
                'selasa_keluar',
                'rabu_masuk',
                'rabu_keluar',
                'kamis_masuk',
                'kamis_keluar',
                'jumat_masuk',
                'jumat_keluar',
                'sabtu_masuk',
                'sabtu_keluar',
                'minggu_masuk',
                'minggu_keluar',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('jadwal_dokter', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('jadwal_dokter', 'hari')) {
                $table->string('hari')->after('poliklinik');
            }
            if (!Schema::hasColumn('jadwal_dokter', 'jam_masuk')) {
                $table->time('jam_masuk')->nullable()->after('hari');
            }
            if (!Schema::hasColumn('jadwal_dokter', 'jam_keluar')) {
                $table->time('jam_keluar')->nullable()->after('jam_masuk');
            }
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
            // Add old day columns back
            $table->string('senin')->nullable()->after('poliklinik');
            $table->string('selasa')->nullable()->after('senin');
            $table->string('rabu')->nullable()->after('selasa');
            $table->string('kamis')->nullable()->after('rabu');
            $table->string('jumat')->nullable()->after('kamis');
            $table->string('sabtu')->nullable()->after('jumat');
            $table->string('minggu')->nullable()->after('sabtu');

            $table->time('senin_masuk')->nullable()->after('minggu');
            $table->time('senin_keluar')->nullable()->after('senin_masuk');
            $table->time('selasa_masuk')->nullable()->after('senin_keluar');
            $table->time('selasa_keluar')->nullable()->after('selasa_masuk');
            $table->time('rabu_masuk')->nullable()->after('selasa_keluar');
            $table->time('rabu_keluar')->nullable()->after('rabu_masuk');
            $table->time('kamis_masuk')->nullable()->after('rabu_keluar');
            $table->time('kamis_keluar')->nullable()->after('kamis_masuk');
            $table->time('jumat_masuk')->nullable()->after('kamis_keluar');
            $table->time('jumat_keluar')->nullable()->after('jumat_masuk');
            $table->time('sabtu_masuk')->nullable()->after('jumat_keluar');
            $table->time('sabtu_keluar')->nullable()->after('sabtu_masuk');
            $table->time('minggu_masuk')->nullable()->after('sabtu_keluar');
            $table->time('minggu_keluar')->nullable()->after('minggu_masuk');

            // Drop new columns
            $table->dropColumn(['hari', 'jam_masuk', 'jam_keluar']);
        });
    }
}
