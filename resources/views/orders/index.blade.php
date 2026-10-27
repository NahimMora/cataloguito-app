<x-app-layout title="Pedidos" subtitle="Solicitudes claras. Conversaciones más simples.">
    <form class="toolbar" method="get">
        <div class="search-field"><x-icon name="search" :size="18" /><input type="search" name="q"
                value="{{ request('q') }}" aria-label="Buscar pedidos" placeholder="Buscar por nombre o número"></div>
        <select name="status" aria-label="Filtrar estado">
            <option value="">Todos los estados</option>
            @foreach (\App\Models\Order::LABELS as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="button secondary">Buscar</button>
    </form>
    <div class="card">
        <div class="card-header">
            <h2>Solicitudes de tu catálogo</h2><span class="badge neutral">{{ $orders->total() }} en este filtro</span>
        </div>
        @forelse($orders as $order)
            <a class="order-row" href="{{ route('orders.show', $order->public_id) }}">
                <div><strong>{{ $order->displayNumber() }} · {{ $order->buyer_name }}</strong>
                    <p>{{ $order->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }} ·
                        {{ $order->items_count }} productos diferentes</p>
                </div><span class="amount">{{ \App\Support\Money::format($order->total_minor) }}</span><span
                    class="badge {{ $order->status }}">{{ \App\Models\Order::LABELS[$order->status] }}</span>
        </a>@empty<div class="empty">
                <div class="empty-icon"><x-icon name="bag" :size="27" /></div>
                <h2>Todavía no hay solicitudes acá.</h2>
                <p>Compartí tu catálogo y encontrá los próximos pedidos en este espacio.</p><a
                    href="{{ route('shop.settings') }}" class="button secondary">Ir a mi catálogo</a>
            </div>
        @endforelse
        <div class="pagination">{{ $orders->links('components.pagination') }}</div>
    </div>
    <p class="field-help" style="margin-top:15px">Una solicitud registrada no significa que el comprador haya enviado el
        mensaje por WhatsApp ni que la venta esté pagada.</p>
</x-app-layout>
