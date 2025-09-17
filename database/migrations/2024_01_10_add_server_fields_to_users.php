<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kode_server')->nullable()->after('status');
            $table->string('password_server')->nullable()->after('kode_server');
            $table->string('vlan')->nullable()->after('password_server');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kode_server', 'password_server', 'vlan']);
        });
    }
};
