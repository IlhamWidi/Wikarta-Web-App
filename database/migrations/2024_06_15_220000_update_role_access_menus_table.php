<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoleAccessMenusTable extends Migration
{
    public function up()
    {
        // Tidak perlu perubahan struktur, hanya pastikan menu_access bertipe json
        // Jika sudah json, migration ini bisa dikosongkan
    }

    public function down()
    {
        //
    }
}
