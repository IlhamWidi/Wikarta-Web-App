<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->string('installation_number')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('package_id')->constrained()->restrictOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->date('completed_date')->nullable();
            $table->time('completed_time')->nullable();
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
                'rescheduled'
            ])->default('scheduled');
            $table->text('installation_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('equipment_used')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // before/after installation photos
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('installation_number');
            $table->index(['customer_id', 'status']);
            $table->index('technician_id');
            $table->index('scheduled_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
};
