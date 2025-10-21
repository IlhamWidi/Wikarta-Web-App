<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$connection = DB::connection();
$database = $connection->getDatabaseName();
$tables = $connection->select('SHOW TABLES');

$key = 'Tables_in_' . $database;

foreach ($tables as $table) {
    $name = $table->$key;
    $count = DB::table($name)->count();
    echo $name . ': ' . $count . PHP_EOL;
}
