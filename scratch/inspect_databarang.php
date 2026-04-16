<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

try {
    $barang = (array) DB::table('databarang')->first();
    echo "Columns in databarang:\n";
    foreach(array_keys($barang) as $key) {
        echo "- " . $key . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
