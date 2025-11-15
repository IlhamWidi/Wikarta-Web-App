<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id')->unique();
            $table->string('type'); // DunningNotification, PaymentReminder, etc
            $table->morphs('notifiable'); // customer_id or user_id
            $table->string('channel'); // email, whatsapp, sms
            $table->string('recipient'); // email address or phone number
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('metadata')->nullable(); // invoice_id, etc
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index('notification_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
