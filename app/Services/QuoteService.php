<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class QuoteService
{
    public function lines(Shop $shop, array $items): array
    {
        $lines = [];
        $ids = array_column($items, 'id');
        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['items' => 'Hay artículos repetidos. Volvé a revisar el carrito.']);
        }
        $products = $shop->products()->where('status', 'active')->where('available', true)->whereIn('public_id', $ids)->get()->keyBy('public_id');
        foreach ($items as $item) {
            $p = $products->get($item['id']);
            if (! $p) {
                throw ValidationException::withMessages(['items' => 'Un producto ya no está disponible. Quitalo del carrito y volvé a revisar.']);
            }
            $lines[] = ['id' => $p->public_id, 'product_id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'quantity' => (int) $item['quantity'], 'unit_price_minor' => $p->price_minor, 'total_minor' => $p->price_minor * (int) $item['quantity']];
        }
        usort($lines, fn ($a, $b) => strcmp($a['id'], $b['id']));

        return $lines;
    }

    public function issue(Shop $shop, array $items, string $session): array
    {
        $lines = $this->lines($shop, $items);

        return ['lines' => $lines, 'total_minor' => array_sum(array_column($lines, 'total_minor')), 'token' => Crypt::encryptString(json_encode(['shop' => $shop->id, 'session' => $session, 'expires' => time() + 900, 'lines' => $lines], JSON_THROW_ON_ERROR))];
    }

    public function verify(string $token, Shop $shop, array $lines, string $session): void
    {
        try {
            $quote = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            throw ValidationException::withMessages(['quote' => 'Volvé a revisar el pedido para actualizar sus precios.']);
        }
        if (($quote['shop'] ?? null) !== $shop->id || ($quote['session'] ?? null) !== $session || ($quote['expires'] ?? 0) < time() || ($quote['lines'] ?? []) !== $lines) {
            throw ValidationException::withMessages(['quote' => 'Los precios o la disponibilidad cambiaron. Volvé a revisar el pedido antes de confirmarlo.']);
        }
    }
}
