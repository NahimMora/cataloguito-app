@props(['template', 'style', 'compact' => false])
<div class="template-preview family-{{ $template['family'] }}" style="--demo-accent:{{ $template['accent'] }};--demo-surface:{{ $template['surface'] }}">
    <div class="template-preview-head"><span class="template-demo-logo">{{ mb_substr($template['shop'], 0, 1) }}</span><div><strong>{{ $template['shop'] }}</strong><small>{{ $template['tagline'] }}</small></div><span class="template-bag"><x-icon name="bag" :size="15" /></span></div>
    <div class="template-pills"><span>Todo</span><span>Novedades</span><span>Favoritos</span></div>
    <div class="template-product-grid">
        @foreach ($template['products'] as [$name, $price, $image])
            <article><img src="/demo/{{ $image }}.webp" alt="" width="180" height="180" loading="lazy"><div><small>{{ $template['sector'] }}</small><strong>{{ $name }}</strong><b>{{ \App\Support\Money::format($price * 100) }}</b></div></article>
        @endforeach
    </div>
</div>
