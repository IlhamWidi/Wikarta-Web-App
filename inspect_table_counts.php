<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'users',
    'menus',
    'role_access_menus',
    'attendances',
    'pelanggans',
    'pemasangans',
    'inventory_stocks',
    'orders',
    'returs',
    'transfer_stocks',
    'invoices',
    'invoice_settings',
    'assets',
    'give_aways',
    'support_tickets',
    'resolve_tickets',
    'website_products',
    'faqs',
    'methods',
    'partners',
];

foreach ($tables as $table) {
    if (!DB::getSchemaBuilder()->hasTable($table)) {
        echo $table . ": table not found" . PHP_EOL;
        continue;
    }

    $count = DB::table($table)->count();
    echo $table . ': ' . $count . PHP_EOL;
}
