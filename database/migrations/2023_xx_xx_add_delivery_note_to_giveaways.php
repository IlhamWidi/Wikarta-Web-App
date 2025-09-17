<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('give_aways', function (Blueprint $table) {
            $table->text('delivery_note')->nullable()->after('recipient_photo');
        });
    }

    public function down()
    {
        Schema::table('give_aways', function (Blueprint $table) {
            $table->dropColumn('delivery_note');
        });
    }
};
