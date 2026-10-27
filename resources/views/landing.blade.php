@php
    $styles = config('cataloguito.styles');
    $structuredData = ['@'.'context' => 'https://schema.org', '@'.'type' => 'SoftwareApplication', 'name' => 'Cataloguito', 'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web', 'description' => 'Plataforma para crear catálogos digitales actualizables y recibir solicitudes por WhatsApp.', 'offers' => ['@'.'type' => 'Offer', 'price' => '0', 'priceCurrency' => 'ARS'], 'url' => url('/')];
@endphp
<x-base title="Catálogo digital para WhatsApp · Cataloguito">
    @push('head')
        <meta name="description" content="Creá un catálogo digital para WhatsApp, actualizá precios desde el celular y recibí solicitudes ordenadas. Gratis, sin tarjeta.">
        <link rel="canonical" href="{{ url('/') }}">
        <meta property="og:type" content="website"><meta property="og:title" content="Cataloguito · Tu catálogo online, siempre al día"><meta property="og:description" content="Mostrá lo que vendés, actualizá precios desde el celu y recibí pedidos claros."><meta property="og:url" content="{{ url('/') }}"><meta property="og:image" content="{{ url('/cataloguito-hero.webp') }}">
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
    <div class="cataloguito-landing">
        <header class="public-nav landing-nav"><x-brand /><nav class="public-nav-links" aria-label="Principal"><a class="hide-mobile" href="{{ route('templates.index') }}">Plantillas</a><a href="{{ route('login') }}">Ingresar</a><a class="button warm" href="{{ route('register') }}">Crear catálogo</a></nav></header>
        <main id="main"><x-alerts />
            <section class="cataloguito-hero">
                <div class="hero-copy"><p class="hero-kicker">TU NEGOCIO, LINDO Y SIEMPRE AL DÍA</p><h1>Tu catálogo online, listo para compartir por WhatsApp.</h1><p class="hero-lead">Mostrá lo que vendés, actualizá precios desde el celu y recibí solicitudes ordenadas.</p><div class="landing-actions"><a class="button butter" href="{{ route('register') }}">Crear mi catálogo gratis <x-icon name="arrow" :size="17" /></a><a class="button coral-outline" href="#plantillas">Ver plantillas</a></div><p class="landing-proof"><x-icon name="check" :size="15" />Sin tarjeta · Hasta 50 productos · Tu enlace permanente</p></div>
                <div class="hero-product-demo"><img src="/cataloguito-hero.webp" srcset="/cataloguito-hero-800.webp 800w, /cataloguito-hero.webp 1536w" sizes="(max-width:850px) calc(100vw - 36px), 54vw" alt="Ropa, cosmética, juguete, accesorio para mascotas, bazar y pastelería sobre una mesa" width="1536" height="1024" fetchpriority="high"><div class="price-change-card"><span>Remera terracota</span><small>Precio actualizado</small><strong>$ 18.500</strong></div><div class="live-badge"><i></i> Listo para compartir</div></div>
            </section>
            <section class="landing-templates" id="plantillas" x-data="{ selected: 'casa' }">
                <div class="section-heading"><p class="eyebrow">SE PARECE A TU NEGOCIO</p><h2>Elegí un estilo y hacelo tuyo.</h2><p>Probá las plantillas. Tus productos y precios quedan siempre en el mismo lugar.</p></div>
                <div class="template-tabs" role="tablist" aria-label="Rubros de plantillas">
                    @foreach ($styles as $key => $template)<button type="button" role="tab" :aria-selected="selected === '{{ $key }}'" :class="selected === '{{ $key }}' ? 'active' : ''" @click="selected='{{ $key }}'">{{ $template['sector'] }}</button>@endforeach
                </div>
                <div class="landing-template-stage">
                    @foreach ($styles as $key => $template)
                        <div x-show="selected === '{{ $key }}'" x-cloak x-transition.opacity.duration.180ms class="landing-template-panel" role="tabpanel">
                            <div class="template-description"><span>{{ $template['sector'] }}</span><h3>{{ $template['name'] }}</h3><p>{{ $template['tagline'] }}</p><div class="template-actions"><a class="button secondary" href="{{ route('templates.show', $key) }}">Ver demo completa</a><a class="button dark" href="{{ route('register', ['style' => $key]) }}">Usar este estilo</a></div></div>
                            <x-template-preview :template="$template" :style="$key" />
                        </div>
                    @endforeach
                </div><a class="all-templates-link" href="{{ route('templates.index') }}">Ver las siete plantillas juntas <x-icon name="arrow" :size="16" /></a>
            </section>
            <section class="compact-steps" id="como-funciona"><div class="section-heading"><p class="eyebrow">EN TRES PASOS</p><h2>De tus productos a un enlace compartible.</h2></div><div class="step-grid"><article><b>01</b><h3>Cargá lo que vendés</h3><p>Foto, nombre y precio. Sin formularios interminables.</p></article><article><b>02</b><h3>Elegí tu estilo</h3><p>Colores y una plantilla pensada para tu rubro.</p></article><article><b>03</b><h3>Compartí y recibí</h3><p>Tu cliente arma la solicitud y sigue por WhatsApp.</p></article></div></section>
            <section class="landing-includes"><div><p class="eyebrow">ARRANCÁ GRATIS</p><h2>Lo necesario para mostrar y vender mejor.</h2><ul><li><x-icon name="check" :size="17" />Catálogo público y enlace permanente</li><li><x-icon name="check" :size="17" />Hasta 50 productos con fotos</li><li><x-icon name="check" :size="17" />Carrito y solicitudes registradas</li><li><x-icon name="check" :size="17" />Actualización de precios desde el celular</li></ul><a class="button butter" href="{{ route('register') }}">Empezar gratis</a></div><div class="landing-faq"><details><summary>¿Necesito saber de tecnología?</summary><p>No. Si sabés subir una foto y escribir un precio, podés armar tu catálogo.</p></details><details><summary>¿Se envía el pedido solo por WhatsApp?</summary><p>No. Cataloguito guarda la solicitud y abre un mensaje preparado para que el comprador lo revise y lo envíe.</p></details><details><summary>¿Puedo cambiar precios después?</summary><p>Sí. El mismo enlace muestra el precio actualizado en la próxima visita.</p></details><details><summary>¿Puedo cobrar online?</summary><p>El MVP confirma pedidos por conversación. Los pagos online no están activos todavía.</p></details></div></section>
            <section class="landing-final"><p>Tu negocio ya tiene algo especial.</p><h2>Dale un lugar lindo para mostrarlo.</h2><a class="button butter" href="{{ route('register') }}">Crear mi Cataloguito <x-icon name="arrow" :size="17" /></a></section>
        </main>
        <footer class="landing-footer"><span>Cataloguito. Hecho con cuidado, en Argentina.</span><div class="row gap"><a href="{{ route('templates.index') }}">Plantillas</a><a href="{{ route('seo.whatsapp') }}">Catálogo para WhatsApp</a><a href="{{ route('seo.free') }}">Catálogo gratis</a><a href="{{ route('privacy') }}">Privacidad</a><a href="{{ route('terms') }}">Términos</a></div></footer>
    </div>
</x-base>
