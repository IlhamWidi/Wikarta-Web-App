<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pemasangans', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('pelanggan_id', 36);
            $table->string('panjang_kabel', 255)->nullable();
            $table->string('odp', 255)->nullable();
            $table->string('opm', 255)->nullable();
            $table->string('kode_ppoe', 255)->nullable();
            $table->string('user_ppoe', 255)->nullable();
            $table->string('password_ppoe', 255)->nullable();
            $table->string('vlan_ppoe', 255)->nullable();
            $table->string('foto_rumah', 255)->nullable();
            $table->string('teknisi_id', 36);
            $table->enum('status', ['REGISTER', 'SEDANG_DIPROSES', 'SELESAI'])->default('REGISTER');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemasangans');
    }
};
