@props(['title' => 'Mi negocio', 'subtitle' => ''])
<x-base :title="$title . ' · Vitrina'">
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand"><x-brand /><span class="workspace-label">TU ESPACIO DE VENTAS</span></div>
            <div class="shop-switch"><span class="avatar">{{ mb_substr($currentShop->name, 0, 1) }}</span>
                <div><strong>{{ $currentShop->name }}</strong><small>Mi negocio <span class="tiny-dot"></span></small>
                </div><x-icon name="chevron" :size="16" />
            </div>
            <nav aria-label="Principal" class="side-nav">
                @foreach ([['dashboard', 'grid', 'Inicio'], ['products.*', 'box', 'Productos'], ['orders.*', 'bag', 'Pedidos'], ['shop.*', 'palette', 'Mi catálogo'], ['account*', 'settings', 'Cuenta y plan']] as [$route, $icon, $label])
                    @php(
    $target = match ($route) {
        'products.*' => 'products.index',
        'orders.*' => 'orders.index',
        'shop.*' => 'shop.settings',
        'account*' => 'account',
        default => $route
    }
)
                    <a href="{{ route($target) }}" class="{{ request()->routeIs($route) ? 'active' : '' }}"
                        @if (request()->routeIs($route)) aria-current="page" @endif><x-icon
                            :name="$icon" /><span>{{ $label }}</span>
                        @if ($route === 'orders.*' && ($pending = $currentShop->orders()->where('status', 'pending')->count()))
                            <span class="nav-count">{{ $pending }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
            <div class="sidebar-bottom">
                <div class="plan-note"><x-icon name="spark" /><strong>Hecho para tu negocio.</strong>
                    <p>Menos vueltas. Más tiempo para lo que hacés.</p><a href="{{ route('account') }}">Conocé tu plan
                        <x-icon name="arrow" :size="15" /></a>
                </div>
                <div class="user-row"><span
                        class="avatar small">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span>{{ auth()->user()->name }}<small>Tu
                            cuenta</small></span>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="icon-button"
                            aria-label="Cerrar sesión"><x-icon name="logout" /></button></form>
                </div>
            </div>
        </aside>
        <div class="app-body">
            <header class="topbar">
                <div class="breadcrumb">Mi negocio <span>/</span> <strong>{{ $title }}</strong></div>
                <div class="row gap"><span
                        class="badge {{ $currentShop->status === 'published' ? 'green' : 'neutral' }}"><span
                            class="status-dot"></span>{{ $currentShop->status === 'published' ? 'Catálogo online' : 'Sin publicar' }}</span><a
                        class="button secondary small" href="{{ route('shop.preview') }}" target="_blank"
                        rel="noopener">Ver catálogo <x-icon name="external" :size="15" /></a></div>
            </header>
            <main id="main" class="app-main">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">TU NEGOCIO, AL DÍA</p>
                        <h1>{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="muted">{{ $subtitle }}</p>
                        @endif
                    </div>
                    @isset($actions)
                        <div class="heading-actions">{{ $actions }}</div>
                    @endisset
                </div>
                <x-alerts />
                @unless (auth()->user()->hasVerifiedEmail())
                    <div class="alert info"><x-icon name="mail" /><span>Verificá tu email para publicar el catálogo. <a
                                href="{{ route('verification.notice') }}">Verificar ahora</a></span></div>
                @endunless
                {{ $slot }}
            </main>
            <footer class="app-footer"><span>Tu negocio tiene mucho para mostrar.</span><span>Hecho con cuidado, en
                    Argentina.</span></footer>
        </div>
        <nav class="mobile-nav" aria-label="Navegación móvil"><a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><x-icon name="grid" />Inicio</a><a
                href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><x-icon name="box" />Productos</a><a
                href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}"><x-icon
                    name="bag" />Pedidos</a><a href="{{ route('shop.settings') }}"
                class="{{ request()->routeIs('shop.*') ? 'active' : '' }}"><x-icon name="palette" />Catálogo</a><a
                href="{{ route('account') }}" class="{{ request()->routeIs('account*') ? 'active' : '' }}"><x-icon
                    name="settings" />Cuenta</a></nav>
    </div>
</x-base>
