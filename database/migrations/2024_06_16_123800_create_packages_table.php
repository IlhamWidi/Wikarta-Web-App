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
        Schema::create('packages', function (Blueprint $table) {
            $table->string('id', 36)->primary();

            $table->string('name');
            $table->string('speed');
            $table->text('spesification');
            $table->unsignedBigInteger('subscribe_price')->nullable();
            $table->unsignedBigInteger('registration_price')->nullable();
            $table->integer('sequence', false, true)->length(11)->nullable();
            $table->boolean('publish')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
