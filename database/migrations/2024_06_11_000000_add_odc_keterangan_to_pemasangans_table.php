<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOdcKeteranganToPemasangansTable extends Migration
{
    public function up()
    {
        Schema::table('pemasangans', function (Blueprint $table) {
            // Ganti 'opm' dengan nama kolom terakhir pada tabel pemasangans
            $table->string('odc', 255)->nullable()->after('opm');
            $table->string('keterangan', 255)->nullable()->after('odc');
        });
    }

    public function down()
    {
        Schema::table('pemasangans', function (Blueprint $table) {
            $table->dropColumn(['odc', 'keterangan']);
        });
    }
}
