<x-base :title="$template['shop'].' · Catálogo de ejemplo'">
    @push('head')<meta name="robots" content="noindex,follow">@endpush
    <div class="demo-notice"><span>Catálogo de ejemplo · {{ $template['sector'] }}</span><div><a href="{{ route('templates.index') }}">Ver otros estilos</a><a class="button warm" href="{{ route('register', ['style' => $style]) }}">Usar {{ $template['name'] }}</a></div></div>
    <main id="main" class="template-demo-page"><x-template-preview :template="$template" :style="$style" /></main>
</x-base>
