<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = DB::table('menus')->select('id', 'parent_id', 'name', 'routes', 'active')->orderBy('id')->get();

foreach ($menus as $menu) {
    echo $menu->id . ' | ' . ($menu->parent_id ?? 'null') . ' | ' . $menu->name . ' | ' . $menu->routes . ' | active=' . $menu->active . PHP_EOL;
}
