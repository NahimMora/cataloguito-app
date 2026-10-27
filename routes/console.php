<?php

use App\Models\Shop;
use App\Services\OutboxProcessor;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('orders:notify {--limit=20}', function (OutboxProcessor $processor) {
    $this->info('Notificaciones enviadas: '.$processor->process(min(100, max(1, (int) $this->option('limit')))));
})->purpose('Enviar un lote corto de notificaciones pendientes');
Schedule::command('orders:notify --limit=20')->everyMinute()->withoutOverlapping(3);
Schedule::call(function () {
    DB::table('local_messages')->where('created_at', '<', now()->subDays(7))->delete();
    DB::table('outbox')->where('status', 'sent')->where('sent_at', '<', now()->subDays(30))->delete();
})->daily()->name('mail-retention')->withoutOverlapping();
Artisan::command('catalog:plan {slug} {plan}', function () {
    if (! in_array($this->argument('plan'), ['free', 'pro'], true)) {
        $this->error('Plan inválido.');

        return 1;
    }
    $shop = Shop::where('slug', $this->argument('slug'))->firstOrFail();
    if ($this->argument('plan') === 'free' && ($shop->products()->where('status', 'active')->count() > 50 || $shop->storage_bytes > 50_000_000)) {
        $this->error('El comercio debe reducir primero sus productos/espacio. No se borraron datos.');

        return 1;
    }
    $shop->update(['plan' => $this->argument('plan')]);
    $this->info('Plan actualizado. Este comando no verifica ni registra cobros.');
})->purpose('Asignar un plan después de conciliación manual del fundador');
