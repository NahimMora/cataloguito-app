{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
    </url>
    <url>
        <loc>{{ route('templates.index') }}</loc>
    </url>
    <url><loc>{{ route('seo.whatsapp') }}</loc></url>
    <url><loc>{{ route('seo.free') }}</loc></url>
    @foreach ($shops as $shop)
        <url>
            <loc>{{ $shop->publicUrl() }}</loc>
            <lastmod>{{ $shop->updated_at->toAtomString() }}</lastmod>
        </url>
        @foreach ($shop->products as $product)
            <url>
                <loc>{{ $product->publicUrl($shop) }}</loc>
                <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
            </url>
        @endforeach
    @endforeach
</urlset>
