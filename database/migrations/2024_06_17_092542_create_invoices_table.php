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
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('id', 36)->primary();

            $table->string('branch_id', 36)->nullable();
            $table->integer('user_id', false, true)->length(11)->nullable();
            $table->date('due_date')->nullable();
            $table->date('paid_off_date')->nullable();
            $table->date('last_notification_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_description')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->enum('invoice_status', ['UNPAID', 'SETTLEMENT']);
            $table->enum('payment_method', ['CASH', 'MIDTRANS']);
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
