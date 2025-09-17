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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['ADMIN', 'CUSTOMER']);
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('lampiran_foto_ktp')->nullable();
            $table->string('lampiran_foto_rumah')->nullable();
            $table->string('branch_id', 36)->nullable();
            $table->string('package_id', 36)->nullable();
            $table->unsignedBigInteger('subscribe_price')->nullable();
            $table->unsignedBigInteger('registration_price')->nullable();
            $table->string('coordinates', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('role_id')->nullable();
            $table->string('city_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('status')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
