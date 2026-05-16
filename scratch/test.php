<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('catatan_keperawatan_ralan');
file_put_contents(__DIR__ . '/schema.json', json_encode($columns));
