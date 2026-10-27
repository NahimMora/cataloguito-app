<x-app-layout :title="'Pedido ' . $order->displayNumber()" :subtitle="$order->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y · H:i') .
    ' · Solicitud de tu catálogo'"><x-slot:actions><a href="{{ route('orders.index') }}"
            class="button secondary">Volver a pedidos</a></x-slot:actions>
    <div class="form-layout">
        <div class="stack">
            <section class="card">
                <div class="card-header">
                    <h2>Lo que eligió tu cliente</h2><span
                        class="badge {{ $order->status }}">{{ \App\Models\Order::LABELS[$order->status] }}</span>
                </div>
                <div class="card-padding">
                    @foreach ($order->items as $line)
                        <div class="receipt-item">
                            <div><strong>{{ $line->quantity }} × {{ $line->name }}</strong><small
                                    style="display:block;margin-top:5px">{{ \App\Support\Money::format($line->unit_price_minor) }}
                                    por unidad @if ($line->sku)
                                        · {{ $line->sku }}
                                    @endif
                                </small>
                            </div><strong>{{ \App\Support\Money::format($line->total_minor) }}</strong>
                        </div>
                    @endforeach
                    <div class="cart-total">
                        <span>Total estimado</span><span>{{ \App\Support\Money::format($order->total_minor) }}</span>
                    </div>
                    <p class="field-help">Precios guardados al confirmar la solicitud. Pago y entrega se coordinan con
                        el cliente.</p>
                </div>
            </section>
            <section class="card card-padding">
                <h2 style="margin-bottom:20px">Historial del pedido</h2>
                <ol class="timeline">
                    @foreach ($order->events as $event)
                        <li>{{ \App\Models\Order::LABELS[$event->to_status] }}<small>{{ $event->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }}</small>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
        <aside class="stack">
            <section class="card card-padding">
                <h2 style="margin-bottom:20px">Tu cliente</h2>
                <div class="summary-list">
                    <div><span class="muted">Nombre</span><strong>{{ $order->buyer_name }}</strong></div>
                    <div><span class="muted">Teléfono informado</span><strong>{{ $order->buyer_phone }}</strong></div>
                </div>
                @if ($order->note)
                    <p style="font-size:12px;margin-top:20px;white-space:pre-line">{{ $order->note }}</p>
                @endif
                <a href="https://wa.me/{{ $order->buyer_phone }}?text={{ rawurlencode('Hola ' . $order->buyer_name . ', te escribo por tu pedido ' . $order->displayNumber() . ' de ' . $currentShop->name . '.') }}"
                    target="_blank" rel="noopener noreferrer" class="button dark full" style="margin-top:25px">
                    <x-icon name="phone" :size="16" />Continuar por WhatsApp</a>
            </section>
            <section class="card card-padding">
                <h2 style="margin-bottom:15px">Próximo paso</h2>
                <p class="field-help" style="margin-bottom:18px">Confirmá disponibilidad con el cliente antes de
                    avanzar. El estado no verifica un pago.</p>
                @foreach (match ($order->status) {
        'pending' => ['confirmed' => 'Confirmar pedido', 'cancelled' => 'Cancelar solicitud'],
        'confirmed' => ['completed' => 'Marcar completado', 'cancelled' => 'Cancelar pedido'],
        default => [],
    } as $state => $label)
                    <form method="post" action="{{ route('orders.status', $order->public_id) }}"
                        style="margin-top:10px">@csrf @method('PATCH')<input name="status" type="hidden"
                            value="{{ $state }}"><button
                            class="button {{ $state === 'cancelled' ? 'secondary' : 'dark' }} full">{{ $label }}</button>
                    </form>
                    @endforeach @if (in_array($order->status, ['completed', 'cancelled']))
                        <span
                            class="badge {{ $order->status }}">{{ \App\Models\Order::LABELS[$order->status] }}</span>
                    @endif
            </section>
        </aside>
    </div>
</x-app-layout>
