<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOdpOpmOdcKeteranganToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('odp', 255)->nullable()->after('phone_number');
            $table->string('opm', 255)->nullable()->after('odp');
            $table->string('odc', 255)->nullable()->after('opm');
            $table->string('keterangan', 255)->nullable()->after('odc');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['odp', 'opm', 'odc', 'keterangan']);
        });
    }
}
