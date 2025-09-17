<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->decimal('in_lat', 10, 7)->nullable();
            $table->decimal('in_lng', 10, 7)->nullable();
            $table->time('out_time')->nullable();
            $table->decimal('out_lat', 10, 7)->nullable();
            $table->decimal('out_lng', 10, 7)->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
