<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = DB::table('menus')->select('id', 'name', 'photo')->orderBy('id')->get();

foreach ($menus as $menu) {
    echo $menu->id . ' | ' . $menu->name . ' | ' . ($menu->photo ?? 'NULL') . PHP_EOL;
}
