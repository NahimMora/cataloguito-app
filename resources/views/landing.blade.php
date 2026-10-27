<x-base>
    @push('head')
        <meta name="description"
            content="Tu catálogo online, siempre actualizado. Mostrá tus productos, cambiá precios desde el celular y recibí solicitudes por WhatsApp.">
        <link rel="canonical" href="{{ url('/') }}">
    @endpush
    <header class="public-nav"><x-brand />
        <nav class="public-nav-links" aria-label="Principal"><a class="hide-mobile" href="#como-funciona">Cómo
                funciona</a><a href="{{ route('login') }}">Ingresar</a><a class="button dark"
                href="{{ route('register') }}">Crear mi catálogo <x-icon name="arrow" :size="16" /></a></nav>
    </header>
    <main id="main"><x-alerts />
        <section class="landing-hero">
            <div>
                <p class="eyebrow">TU NEGOCIO MERECE SU PROPIO ESPACIO</p>
                <h1>Lo que hacés,<br>bien a la vista.<br><em>Siempre al día.</em></h1>
                <p>Un catálogo con tu identidad. Precios que actualizás en segundos. Y pedidos que llegan a tu WhatsApp,
                    con todo más claro.</p>
                <div class="landing-actions"><a class="button dark" href="{{ route('register') }}">Crear mi catálogo
                        gratis <x-icon name="arrow" :size="16" /></a>
                    @if (\App\Models\Shop::where('slug', 'casa-luma')->where('status', 'published')->exists())
                        <a class="button secondary" href="/casa-luma">Explorar un catálogo</a>
                    @endif
                </div>
                <p class="landing-proof"><x-icon name="check" :size="14" />Sin tarjeta · Hasta 50 productos ·
                    Tu enlace permanente</p>
            </div>
            <div class="hero-display" aria-hidden="true">
                <div class="hero-phone">
                    <div class="phone-handle"></div>
                    <h3>Casa Luma</h3>
                    <p>Objetos para los pequeños rituales.</p>
                    <div class="mini-store-grid">
                        @foreach (['mug', 'bottle', 'bag', 'lamp'] as $demo)
                            <div class="mini-item"><img src="/demo/{{ $demo }}.webp" alt=""
                                    width="180" height="180"></div>
                        @endforeach
                    </div>
                    <div class="phone-cart"><span>Tu pedido · 3 productos</span><strong>Ver pedido →</strong></div>
                </div>
                <div class="floating-tag one"><x-icon name="check" :size="17" /><span>Precio
                        actualizado.<br><strong>Así de simple.</strong></span></div>
                <div class="floating-tag two"><x-icon name="bag" :size="19" /><span>Nueva
                        solicitud<br><strong>Todo en un solo lugar.</strong></span></div>
            </div>
        </section>
        <section class="landing-section" id="como-funciona">
            <p class="eyebrow">MENOS VUELTAS, MÁS NEGOCIO</p>
            <h2>De tus productos a su próximo pedido.</h2>
            <p class="muted">Una herramienta simple para quienes venden con dedicación.</p>
            <div class="feature-grid">
                <article class="card feature-card"><x-icon name="box" :size="27" />
                    <h3>Cargá lo que vendés.</h3>
                    <p>Foto, nombre y precio. Organizá tus productos sin formularios interminables.</p>
                </article>
                <article class="card feature-card"><x-icon name="palette" :size="27" />
                    <h3>Hacelo bien tuyo.</h3>
                    <p>Elegí un estilo y tus colores. Compartí el mismo enlace aunque cambien tus productos.</p>
                </article>
                <article class="card feature-card"><x-icon name="bag" :size="27" />
                    <h3>Recibí pedidos claros.</h3>
                    <p>Tus clientes eligen, revisan y te consultan por WhatsApp. Vos confirmás disponibilidad y pago.
                    </p>
                </article>
            </div>
        </section>
        <section class="landing-section">
            <div class="welcome-banner">
                <div>
                    <p class="eyebrow">EMPEZÁ POR LO SIMPLE</p>
                    <h2>Tu próxima vidriera está a un enlace.</h2>
                    <p>Creá tu espacio y publicá tus primeros productos.</p><a href="{{ route('register') }}"
                        class="button dark">Crear mi catálogo <x-icon name="arrow" :size="16" /></a>
                </div>
            </div>
        </section>
    </main>
    <footer class="landing-footer"><span>Vitrina. Hecho con cuidado, en Argentina.</span>
        <div class="row gap"><a href="{{ route('privacy') }}">Privacidad</a><a href="{{ route('terms') }}">Términos</a>
        </div>
    </footer>
</x-base>
