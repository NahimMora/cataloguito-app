@props(['template', 'style', 'color' => null])
@php($accent = $color ?: $template['accent'])
<section class="template-showcase showcase-{{ $style }} family-{{ $template['family'] }}"
    style="--showcase-accent:{{ $accent }};--showcase-surface:{{ $template['surface'] }}" x-data>
    <header class="showcase-header">
        <a href="#" class="showcase-identity" @click.prevent><span>{{ mb_substr($template['shop'], 0, 1) }}</span><div><strong>{{ $template['shop'] }}</strong><small>{{ $template['sector'] }}</small></div></a>
        <nav aria-label="Navegación de la muestra"><a href="#coleccion">Productos</a><a href="#informacion">Información</a></nav>
        <button type="button" class="showcase-cart" aria-label="Abrir pedido de muestra" @click="$dispatch('notice', 'Esta es una demo. Creá tu catálogo para recibir pedidos.')"><x-icon name="bag" :size="16" /> Mi pedido <b>2</b></button>
    </header>
    <div class="showcase-hero">
        <div class="showcase-hero-copy"><p>{{ $template['eyebrow'] }}</p><h1>{{ $template['tagline'] }}</h1><span>Una selección pensada para encontrar lo que buscás y pedirlo sin vueltas.</span><a href="#coleccion">Ver colección <x-icon name="arrow" :size="15" /></a></div>
        <div class="showcase-feature">
            @php([$featuredName, $featuredPrice, $featuredImage] = $template['products'][0])
            <img src="/demo/{{ $featuredImage }}.webp" alt="{{ $featuredName }}" width="720" height="720" fetchpriority="high">
            <div><small>Recomendado</small><strong>{{ $featuredName }}</strong><b>{{ \App\Support\Money::format($featuredPrice * 100) }}</b></div>
        </div>
    </div>
    <div class="showcase-benefits" id="informacion"><span><b>01</b> Precios actualizados</span><span><b>02</b> Pedido simple</span><span><b>03</b> Atención por WhatsApp</span></div>
    <div class="showcase-collection" id="coleccion">
        <div class="showcase-collection-head"><div><p>Explorá el catálogo</p><h2>{{ $style === 'volumen' ? 'Precios por bulto' : 'Elegidos para vos' }}</h2></div><div class="showcase-categories"><span>Todo</span><span>Novedades</span><span>{{ $style === 'dulce' ? 'Para regalar' : 'Favoritos' }}</span></div></div>
        <div class="showcase-products">
            @foreach ($template['products'] as [$name, $price, $image])
                <article><div class="showcase-product-image"><img src="/demo/{{ $image }}.webp" alt="{{ $name }}" width="720" height="720" loading="{{ $loop->first ? 'eager' : 'lazy' }}"><button type="button" aria-label="Agregar {{ $name }}" @click="$dispatch('notice', 'Producto de muestra. En tu catálogo abrirá el pedido.')"><x-icon name="plus" :size="16" /></button></div><div><small>{{ $template['sector'] }}</small><h3>{{ $name }}</h3><strong>{{ \App\Support\Money::format($price * 100) }}</strong>@if($style === 'volumen')<span>Precio unitario aprox.: {{ \App\Support\Money::format((int) round($price * 100 / ($loop->index ? 6 : 12))) }}</span>@endif</div></article>
            @endforeach
        </div>
    </div>
    <footer class="showcase-footer"><div><strong>{{ $template['shop'] }}</strong><span>{{ $template['tagline'] }}</span></div><p>Catálogo de muestra · Pago y entrega a coordinar con el comercio.</p></footer>
</section>
