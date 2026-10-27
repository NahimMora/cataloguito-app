@php
    $isWhatsapp = $guide === 'whatsapp';
    $title = $isWhatsapp ? 'Catálogo para WhatsApp: productos y pedidos claros' : 'Crear un catálogo digital gratis para tu negocio';
    $description = $isWhatsapp ? 'Creá un catálogo para compartir por WhatsApp, con precios actualizados, carrito y solicitudes ordenadas.' : 'Armá gratis un catálogo digital con tus productos, fotos y precios. Actualizalo desde el celular y compartí siempre el mismo enlace.';
@endphp
<x-base :title="$title.' · Cataloguito'">
    @push('head')<meta name="description" content="{{ $description }}"><link rel="canonical" href="{{ url()->current() }}"><meta property="og:type" content="article"><meta property="og:title" content="{{ $title }}"><meta property="og:description" content="{{ $description }}">@endpush
    <header class="public-nav"><x-brand /><nav class="public-nav-links" aria-label="Principal"><a href="{{ route('templates.index') }}">Plantillas</a><a href="{{ route('login') }}">Ingresar</a><a class="button warm" href="{{ route('register') }}">Crear catálogo</a></nav></header>
    <main id="main" class="seo-guide">
        <p class="eyebrow">GUÍA CORTA PARA EMPEZAR</p><h1>{{ $title }}</h1><p class="seo-guide-lead">{{ $description }}</p>
        @if ($isWhatsapp)
            <section><h2>Un enlace evita mandar fotos y precios uno por uno</h2><p>El catálogo reúne tus productos en una página que podés compartir en una conversación, un estado o tu perfil. Cuando actualizás un precio, el mismo enlace muestra la información nueva.</p></section>
            <section><h2>Cómo llega una solicitud</h2><ol><li>La persona abre tu catálogo y elige productos.</li><li>Revisa cantidades y total estimado.</li><li>Cataloguito registra la solicitud.</li><li>WhatsApp abre un mensaje preparado para que la persona lo revise y lo envíe.</li></ol><p>Abrir WhatsApp no significa que el mensaje fue enviado ni que la venta está pagada. El comercio confirma disponibilidad, entrega y forma de pago.</p></section>
        @else
            <section><h2>Qué necesitás para publicar</h2><p>Nombre del negocio, un WhatsApp, fotos, nombres y precios. Elegís una plantilla y recibís una dirección permanente para compartir.</p></section>
            <section><h2>Qué incluye el plan inicial</h2><ul><li>Hasta 50 productos.</li><li>Categorías, búsqueda y carrito.</li><li>Actualización de precios desde el celular.</li><li>Solicitudes registradas y continuidad por WhatsApp.</li></ul><p>No necesitás tarjeta para crear la cuenta. Pagos online, stock por variantes y facturación no forman parte del MVP actual.</p></section>
        @endif
        <aside class="seo-guide-cta"><div><h2>Elegí un estilo para tu negocio.</h2><p>Ropa, mayorista, cosmética, bazar, juguetes, petshop o pastelería.</p></div><div><a class="button secondary" href="{{ route('templates.index') }}">Ver plantillas</a><a class="button dark" href="{{ route('register') }}">Crear mi catálogo gratis</a></div></aside>
        <nav class="related-guides" aria-label="Guías relacionadas">@if ($isWhatsapp)<a href="{{ route('seo.free') }}">Cómo crear un catálogo digital gratis →</a>@else<a href="{{ route('seo.whatsapp') }}">Cómo usar un catálogo para WhatsApp →</a>@endif</nav>
    </main>
</x-base>
