<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$legacyRoutes = [
    'transfer-stocks.index',
    'orders.index',
    'returs.index',
    'pemasangans.index',
    'invoices.index',
    'assets.index',
    'give-aways.index',
    'support-tickets.index',
    'attendances.index',
    'users.index',
];

$deleted = DB::table('menus')->whereIn('routes', $legacyRoutes)->delete();

echo "Deleted $deleted legacy menu rows" . PHP_EOL;
