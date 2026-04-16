<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\RegPeriksa;
$reg = RegPeriksa::first();
if ($reg) {
    echo "NO_RAWAT: " . $reg->no_rawat . "\n";
} else {
    echo "No reg found\n";
}
