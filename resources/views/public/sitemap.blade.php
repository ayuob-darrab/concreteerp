@php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $p)
    <url>
        <loc>{{ $p['url'] }}</loc>
        <changefreq>{{ $p['freq'] }}</changefreq>
        <priority>{{ $p['priority'] }}</priority>
    </url>
@endforeach
</urlset>
