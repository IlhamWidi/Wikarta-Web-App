<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->string('giveaway_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['voucher', 'discount', 'free_month', 'merchandise', 'other']);
            $table->decimal('value', 15, 2)->nullable(); // discount amount or value
            $table->integer('percentage')->nullable(); // discount percentage
            $table->integer('quantity')->default(1);
            $table->integer('claimed')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            $table->json('terms_conditions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('giveaway_code');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giveaways');
    }
};
