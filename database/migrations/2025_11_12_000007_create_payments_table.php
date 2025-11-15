<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('order_id')->unique(); // Midtrans order_id
            $table->string('transaction_id')->nullable(); // Midtrans transaction_id
            $table->enum('payment_method', [
                'bank_transfer',
                'virtual_account',
                'qris',
                'credit_card',
                'e_wallet',
                'cash',
                'other'
            ]);
            $table->string('payment_type')->nullable(); // VA bank name, e-wallet provider, etc
            $table->decimal('amount', 15, 2);
            $table->enum('status', [
                'pending',
                'settlement',
                'capture',
                'deny',
                'cancel',
                'expire',
                'refund',
                'partial_refund'
            ])->default('pending');
            $table->string('va_number')->nullable();
            $table->string('bill_key')->nullable();
            $table->string('biller_code')->nullable();
            $table->text('payment_url')->nullable();
            $table->text('qr_code_url')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->timestamp('expiry_time')->nullable();
            $table->json('metadata')->nullable(); // store Midtrans response
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('payment_code');
            $table->index('transaction_id');
            $table->index(['invoice_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
