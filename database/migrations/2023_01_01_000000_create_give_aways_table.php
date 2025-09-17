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
        Schema::create('give_aways', function (Blueprint $table) {
            $table->id();
            $table->string('give_away', 255)->nullable();
            $table->string('recipient_photo')->nullable();
            // ... tambahkan kolom lain sesuai kebutuhan ...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('give_aways');
    }
};
