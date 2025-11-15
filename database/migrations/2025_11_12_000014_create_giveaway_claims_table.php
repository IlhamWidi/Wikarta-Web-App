<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giveaway_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giveaway_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->timestamp('claimed_at');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete(); // if used on invoice
            $table->enum('status', ['claimed', 'used', 'expired', 'cancelled'])->default('claimed');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['giveaway_id', 'customer_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giveaway_claims');
    }
};
