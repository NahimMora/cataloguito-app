<?php

namespace App\Services;

use App\Models\Order;
use App\Support\Money;

class Whatsapp
{
    public static function message(Order $order): string
    {
        $message = "Hola, quiero consultar por este pedido de {$order->shop->name}:\n\nPedido {$order->displayNumber()}\n\n";
        foreach ($order->items as $line) {
            $message .= "{$line->quantity} × {$line->name} — ".Money::format($line->total_minor)."\n";
        }
        $message .= "\nTotal estimado: ".Money::format($order->total_minor)."\n\n¿Me confirmás disponibilidad y forma de pago?\n\nSoy {$order->buyer_name}.";
        if (strlen(rawurlencode($message)) > 1700) {
            $message = "Hola, soy {$order->buyer_name}. Quiero consultar el pedido {$order->displayNumber()} de {$order->shop->name}. Total estimado: ".Money::format($order->total_minor).'. Podés ver el detalle en tu panel: '.route('orders.show', $order->public_id)."\n¿Me confirmás disponibilidad y forma de pago?";
        }

        return $message;
    }

    public static function url(Order $order): string
    {
        return 'https://wa.me/'.$order->shop->whatsapp.'?text='.rawurlencode(self::message($order));
    }
}
