<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_products', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('category_id', 36)->nullable();
            $table->string('barcode', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->json('mp_prices')->nullable();
            $table->json('admin_fees')->nullable();
            $table->json('stocks')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_products');
    }
};
