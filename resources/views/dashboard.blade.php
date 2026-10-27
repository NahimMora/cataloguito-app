<x-app-layout :title="'Hola, ' . explode(' ', auth()->user()->name)[0] . ' 👋'" subtitle="Todo lo que pasa con tu negocio, en un solo lugar.">
    <x-slot:actions><a href="{{ route('products.create') }}" class="button dark"><x-icon name="plus"
                :size="17" />Agregar producto</a></x-slot:actions>
    <section class="welcome-banner">
        <div>
            <p class="eyebrow">TU CATÁLOGO, TU MEJOR VIDRIERA</p>
            <h2>{{ $counts['products'] ? 'Lo que hacés merece estar a la vista.' : 'Tu próxima venta empieza por mostrar lo tuyo.' }}
            </h2>
            <p>{{ $counts['products'] ? 'Mantené tus precios al día y compartí tu catálogo. Tu negocio está listo para nuevas conversaciones.' : 'Agregá tus primeros productos y compartí un catálogo tan especial como tu negocio.' }}
            </p><a class="button dark small"
                href="{{ $counts['products'] ? route('shop.settings') : route('products.create') }}">{{ $counts['products'] ? 'Mi catálogo' : 'Agregar mi primer producto' }}
                <x-icon name="arrow" :size="15" /></a>
            @php($currentStyle = $styles[$shop->currentStyle()])
            <a class="dashboard-style-pill" href="{{ route('shop.settings') }}#estilo"><span style="--style-color:{{ $shop->appearance_draft['accent'] }}"></span><small>Estilo actual</small><strong>{{ $currentStyle['name'] }} · {{ $currentStyle['sector'] }}</strong><x-icon name="edit" :size="14" /></a>
        </div>
        <div class="welcome-art" aria-hidden="true">
            <div class="mini-store">
                <div class="mini-store-bar"><i></i><i></i><i></i></div>
                <h3>{{ $shop->name }}</h3>
                <div class="mini-store-grid">
                    @foreach (['bag', 'image', 'box', 'spark'] as $icon)
                        <div class="mini-item"><x-icon :name="$icon" /></div>
                    @endforeach
                </div>
            </div>
            <div class="art-chip"><x-icon name="check" :size="17" /><span>Tu catálogo<br><strong>Siempre al
                        día</strong></span></div>
        </div>
    </section>
    <section class="stats-grid" aria-label="Actividad del negocio">
        <article class="card stat">
            <div class="stat-top">Productos activos<span class="stat-icon"><x-icon name="box"
                        :size="17" /></span></div>
            <div class="stat-number">{{ $counts['products'] }}<span
                    style="font-size:12px;color:#a2a5ad;font-weight:500"> / {{ $shop->productLimit() }}</span></div>
            <div class="stat-bottom">Tu selección, lista para compartir</div>
        </article>
        <article class="card stat">
            <div class="stat-top">Solicitudes pendientes<span class="stat-icon"><x-icon name="bag"
                        :size="17" /></span></div>
            <div class="stat-number">{{ $counts['pending'] }}</div>
            <div class="stat-bottom">Por revisar y confirmar con tu cliente</div>
        </article>
        <article class="card stat">
            <div class="stat-top">Vistas esta semana<span class="stat-icon"><x-icon name="external"
                        :size="17" /></span></div>
            <div class="stat-number">{{ $metrics->views }}</div>
            <div class="stat-bottom">{{ $metrics->clicks }} clics para abrir WhatsApp</div>
        </article>
    </section>
    <section class="dashboard-columns">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Últimos pedidos</h2>
                    <p>Cada nueva conversación empieza acá.</p>
                </div><a href="{{ route('orders.index') }}" class="text-link">Ver todos <x-icon name="arrow"
                        :size="14" /></a>
            </div>
            @forelse($recent as $order)
                <a href="{{ route('orders.show', $order->public_id) }}" class="order-row">
                    <div><strong>{{ $order->displayNumber() }} · {{ $order->buyer_name }}</strong>
                        <p>{{ $order->created_at->timezone('America/Argentina/Buenos_Aires')->format('d M, H:i') }}</p>
                    </div><span class="amount">{{ \App\Support\Money::format($order->total_minor) }}</span><span
                        class="badge {{ $order->status }}">{{ \App\Models\Order::LABELS[$order->status] }}</span>
            </a>@empty<div class="empty">
                    <div class="empty-icon"><x-icon name="bag" :size="26" /></div>
                    <h2>Los próximos pedidos empiezan acá.</h2>
                    <p>Compartí tu catálogo. Cuando alguien confirme una solicitud, la vas a encontrar en este espacio.
                    </p>
                </div>
            @endforelse
            <div class="card-footer"><span>Son solicitudes: la disponibilidad se confirma con cada cliente.</span></div>
        </div>
        <aside class="card share-card"><span class="badge {{ $shop->status === 'published' ? 'green' : 'neutral' }}"><span
                    class="status-dot"></span>{{ $shop->status === 'published' ? 'ONLINE' : 'EN PREPARACIÓN' }}</span>
            <h2 style="margin-top:18px">Un enlace. Muchas posibilidades.</h2>
            <p>En tu Instagram, en WhatsApp o en una charla. Tu catálogo va con vos.</p>
            <div class="url-box"><span>{{ $shop->publicUrl() }}</span><button type="button"
                    aria-label="Copiar enlace del catálogo" @click="copyText(@js($shop->publicUrl()))"><x-icon
                        name="copy" :size="16" /></button></div><button class="button dark full"
                @click="copyText(@js($shop->publicUrl()))"><x-icon name="copy" :size="15" />Copiar
                enlace</button><a href="{{ route('products.index') }}" class="button secondary full"
                style="margin-top:9px"><x-icon name="edit" :size="15" />Actualizar precios</a>
            <div class="tip"><x-icon name="spark" :size="21" />
                <div><strong>Un pequeño consejo</strong>
                    <p>Sumá el enlace a tu biografía de Instagram. Tus clientes encuentran los precios sin tener que
                        preguntarte.</p>
                </div>
            </div>
        </aside>
    </section>
</x-app-layout>
