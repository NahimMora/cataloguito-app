<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Services\QuoteService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_two_simultaneous_retries_create_one_order_and_one_notification(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create(['user_id' => $user->id, 'name' => 'Concurrencia', 'slug' => 'concurrencia', 'whatsapp' => '5491100000000', 'status' => 'published', 'appearance' => Shop::defaultAppearance(), 'appearance_draft' => Shop::defaultAppearance()]);
        $p = $shop->products()->create(['name' => 'Producto concurrente', 'slug' => 'producto', 'price_minor' => 1234500]);
        $items = [['id' => $p->public_id, 'quantity' => 2]];
        $session = hash('sha256', 'concurrent-session');
        $quote = app(QuoteService::class)->issue($shop, $items, $session);
        $payload = ['items' => $items, 'quote' => $quote['token'], 'key' => (string) Str::uuid(), 'name' => 'Comprador ficticio', 'phone' => '5491100000000', 'note' => ''];
        $connection = config('database.connections.mysql');
        $environment = array_merge(getenv(), ['APP_ENV' => 'testing', 'APP_KEY' => config('app.key'), 'DB_CONNECTION' => 'mysql', 'DB_HOST' => $connection['host'], 'DB_PORT' => (string) $connection['port'], 'DB_DATABASE' => $connection['database'], 'DB_USERNAME' => $connection['username'], 'DB_PASSWORD' => $connection['password']]);
        $children = [];
        $start = microtime(true) + 1;
        for ($i = 0; $i < 2; $i++) {
            $process = proc_open([PHP_BINARY, base_path('tests/Support/order-worker.php')], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path(), $environment);
            $this->assertIsResource($process);
            fwrite($pipes[0], json_encode(['shop' => $shop->id, 'payload' => $payload, 'session' => $session, 'start_at' => $start], JSON_THROW_ON_ERROR));
            fclose($pipes[0]);
            $children[] = [$process, $pipes];
        }
        $ids = [];
        foreach ($children as [$process,$pipes]) {
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $this->assertSame(0, proc_close($process), $errors);
            $ids[] = json_decode($output, true, 512, JSON_THROW_ON_ERROR)['id'];
        }
        $this->assertSame($ids[0], $ids[1]);
        $this->assertSame(1, $shop->orders()->count());
        $this->assertSame(1, DB::table('outbox')->count());
    }
}
