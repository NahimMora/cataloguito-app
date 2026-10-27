<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Shop;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private QuoteService $quotes) {}

    public function create(Shop $shop, array $data, string $session): Order
    {
        $phone = isset($data['phone_country'])
            ? Phone::normalizeParts($data['phone_country'], $data['phone_local'] ?? '', 'phone_local')
            : Phone::normalize($data['phone'], 'phone');
        $items = $data['items'];
        usort($items, fn ($a, $b) => strcmp($a['id'], $b['id']));
        $payloadHash = hash('sha256', json_encode([$items, trim($data['name']), $phone, trim($data['note'] ?? '')], JSON_THROW_ON_ERROR));

        return DB::transaction(function () use ($shop, $data, $session, $payloadHash, $phone, $items) {
            $locked = Shop::whereKey($shop->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'published', 404);
            $existing = $locked->orders()->where('session_hash', $session)->where('idempotency_key', $data['key'])->first();
            if ($existing) {
                if (! hash_equals($existing->payload_hash, $payloadHash)) {
                    throw ValidationException::withMessages(['key' => 'Este intento ya fue confirmado con otros datos. Abrí un pedido nuevo.']);
                }

                return $existing;
            }
            $lines = $this->quotes->lines($locked, $items);
            $this->quotes->verify($data['quote'], $locked, $lines, $session);
            $locked->increment('order_sequence');
            $order = $locked->orders()->create(['number' => $locked->order_sequence, 'status' => 'pending', 'buyer_name' => trim($data['name']), 'buyer_phone' => $phone, 'note' => trim($data['note'] ?? '') ?: null, 'total_minor' => array_sum(array_column($lines, 'total_minor')), 'currency' => 'ARS', 'session_hash' => $session, 'idempotency_key' => $data['key'], 'payload_hash' => $payloadHash]);
            foreach ($lines as $line) {
                unset($line['id']);
                $order->items()->create($line + ['shop_id' => $locked->id]);
            }
            OrderEvent::create(['shop_id' => $locked->id, 'order_id' => $order->id, 'from_status' => null, 'to_status' => 'pending', 'created_at' => now()]);
            if ($locked->email_orders && $locked->user->hasVerifiedEmail()) {
                DB::table('outbox')->insert(['shop_id' => $locked->id, 'order_id' => $order->id, 'recipient' => $locked->user->email, 'status' => 'pending', 'attempts' => 0, 'next_attempt_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }

            return $order;
        }, 3);
    }

    public function transition(Shop $shop, string $id, string $status, int $userId): void
    {
        DB::transaction(function () use ($shop, $id, $status, $userId) {
            $order = $shop->orders()->where('public_id', $id)->lockForUpdate()->firstOrFail();
            if ($order->status === $status) {
                return;
            }
            $allowed = ['pending' => ['confirmed', 'cancelled'], 'confirmed' => ['completed', 'cancelled'], 'completed' => [], 'cancelled' => []];
            if (! in_array($status, $allowed[$order->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Ese cambio de estado no está permitido. Actualizá la página.']);
            }
            OrderEvent::create(['shop_id' => $shop->id, 'order_id' => $order->id, 'user_id' => $userId, 'from_status' => $order->status, 'to_status' => $status, 'created_at' => now()]);
            $order->update(['status' => $status]);
        });
    }
}
