<x-base :title="'Solicitud ' . $order->displayNumber() . ' · ' . $shop->name">
    <main id="main">
        <div class="card receipt">
            <div class="empty-icon"><x-icon name="check" :size="28" /></div>
            <h1>Tu solicitud quedó registrada.</h1>
            <p class="intro">Pedido {{ $order->displayNumber() }} · {{ $shop->name }}<br>Ahora abrí WhatsApp, revisá
                el mensaje y envialo para seguir la conversación.</p><span class="badge pending">Pendiente de
                confirmación</span>
            <div style="margin-top:20px">
                @foreach ($order->items as $line)
                    <div class="receipt-item"><span>{{ $line->quantity }} ×
                            {{ $line->name }}</span><strong>{{ \App\Support\Money::format($line->total_minor) }}</strong>
                    </div>
                @endforeach
            </div>
            <div class="cart-total"><span>Total
                    estimado</span><span>{{ \App\Support\Money::format($order->total_minor) }}</span></div>
            <p class="field-help">El comercio confirma disponibilidad, pago y entrega. El mensaje todavía requiere que lo
                envíes en WhatsApp.</p>
            <div class="receipt-actions"><a href="{{ \App\Services\Whatsapp::url($order) }}" target="_blank"
                    rel="noopener noreferrer" class="button dark" data-testid="open-whatsapp"><x-icon name="phone"
                        :size="17" />Abrir WhatsApp y revisar mensaje</a><button class="button secondary"
                    @click="copyText(@js(\App\Services\Whatsapp::message($order)))"><x-icon name="copy" :size="16" />Copiar
                    resumen</button><a href="{{ $shop->publicUrl() }}" class="button ghost">Volver al catálogo</a></div>
        </div>
    </main>
</x-base>
