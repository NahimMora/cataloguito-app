<?php

use App\Models\Shop;
use App\Services\OrderService;
use Illuminate\Contracts\Console\Kernel;

// Subprocess helper for the database concurrency test; never exposed over HTTP.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (! app()->environment('testing') || ! str_ends_with(config('database.connections.mysql.database'), '_test')) {
    exit(2);
}
$input = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
$delay = max(0, $input['start_at'] - microtime(true));
if ($delay > 0) {
    usleep((int) ($delay * 1_000_000));
}
try {
    $order = app(OrderService::class)->create(Shop::findOrFail($input['shop']), $input['payload'], $input['session']);
    echo json_encode(['id' => $order->id], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    fwrite(STDERR, get_class($e).': '.$e->getMessage());
    exit(1);
}
