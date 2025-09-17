<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_asset', 255);
            $table->enum('tipe_asset', ['ASSET', 'NON_ASSET']);
            $table->integer('stok')->default(0);
            $table->enum('satuan', ['PCS', 'PAKET', 'METER']);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
