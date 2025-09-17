<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('branch_id', 36)->nullable();
            $table->string('nama_pelanggan', 255)->nullable();
            $table->string('paket_id', 36)->nullable();
            $table->string('nomor_hp', 255)->nullable();
            $table->text('alamat_psb')->nullable();
            $table->string('odp', 255)->nullable();
            $table->string('panjang_kabel', 255)->nullable();
            $table->string('foto_ktp', 255)->nullable();
            $table->string('marketing_id', 36)->nullable();
            $table->string('teknisi_id', 36)->nullable();
            $table->enum('status', ['REGISTER', 'SEDANG_DIPROSES', 'SELESAI'])->default('REGISTER');
            $table->text('keterangan')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelanggan');
    }
};
