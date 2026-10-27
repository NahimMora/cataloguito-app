{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
    </url>
    @foreach ($shops as $shop)
        <url>
            <loc>{{ $shop->publicUrl() }}</loc>
            <lastmod>{{ $shop->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
</urlset>
