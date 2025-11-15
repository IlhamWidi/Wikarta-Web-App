<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('event_key')->unique(); // hash untuk idempotency
            $table->string('order_id');
            $table->string('transaction_id')->nullable();
            $table->string('transaction_status');
            $table->string('payment_type')->nullable();
            $table->string('fraud_status')->nullable();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->json('payload'); // store full webhook payload
            $table->string('signature_key')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('event_key');
            $table->index('order_id');
            $table->index(['order_id', 'transaction_status']);
            $table->index('is_processed');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
