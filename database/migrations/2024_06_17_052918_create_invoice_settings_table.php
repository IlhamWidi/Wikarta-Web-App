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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->string('id', 36)->primary();

            $table->integer('invoice_date', false, true)->length(2)->nullable();
            $table->integer('notification_days_before', false, true)->length(2)->nullable();
            $table->integer('notification_days_warning', false, true)->length(2)->nullable();
            $table->text('notification_template_invoice')->nullable();
            $table->text('notification_template_paid')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
