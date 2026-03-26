<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $p)
    <url>
        <loc>{{ $p['loc'] }}</loc>
        <lastmod>{{ $p['lastmod'] }}</lastmod>
        <priority>{{ $p['priority'] }}</priority>
    </url>
@endforeach
</urlset>

