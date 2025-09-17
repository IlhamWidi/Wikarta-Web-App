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
        Schema::create('transfer_stocks', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->integer('user_id', false, true)->length(11)->nullable();
            $table->string('product_id', 36)->nullable();
            $table->string('source_channel', 255)->nullable();
            $table->string('destination_channel', 255)->nullable();
            $table->integer('qty', false, true)->length(11)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_stocks');
    }
};
