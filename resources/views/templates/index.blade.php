<x-base title="Plantillas para catálogos online · Cataloguito">
    @push('head')
        <meta name="description" content="Conocé los estilos de Cataloguito para ropa, mayoristas, cosmética, bazar, juguetería, petshop y pastelería.">
        <link rel="canonical" href="{{ route('templates.index') }}">
    @endpush
    <header class="public-nav"><x-brand /><nav class="public-nav-links" aria-label="Principal"><a href="{{ route('login') }}">Ingresar</a><a class="button warm" href="{{ route('register') }}">Crear mi catálogo</a></nav></header>
    <main id="main" class="templates-page"><div class="templates-heading"><p class="eyebrow">UN ESTILO PARA CADA NEGOCIO</p><h1>Elegí cómo querés mostrar lo que vendés.</h1><p>Todos los estilos son rápidos, adaptables y fáciles de actualizar desde el celular.</p></div><div class="templates-grid">
        @foreach ($styles as $key => $template)
            <article class="template-card"><div class="row between"><div><span class="template-sector">{{ $template['sector'] }}</span><h2>{{ $template['name'] }}</h2></div><div class="card-palette" aria-label="Paletas incluidas">@foreach($template['palettes'] as $hex => $name)<i style="--swatch:{{ $hex }}" title="{{ $name }}"></i>@endforeach</div></div><x-template-preview :template="$template" :style="$key" compact/><div class="template-actions"><a class="button secondary" href="{{ route('templates.show', $key) }}">Ver demo completa</a><a class="button dark" href="{{ route('register', ['style' => $key]) }}">Usar este estilo</a></div></article>
        @endforeach
    </div></main>
</x-base>
