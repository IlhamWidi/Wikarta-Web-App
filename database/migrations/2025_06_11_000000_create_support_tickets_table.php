<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->string('branch_id', 36)->nullable();
            $table->string('package_id', 36)->nullable();
            $table->string('address')->nullable();
            $table->string('kode_server')->nullable();
            $table->string('password_server')->nullable();
            $table->string('vlan')->nullable();
            $table->string('odp')->nullable();
            $table->string('odc')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('teknisi_id')->nullable();
            $table->enum('status', ['REGISTER', 'PROSES', 'SELESAI', 'DIBATALKAN', 'FORWARD_TEKNISI'])->default('REGISTER');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
}
