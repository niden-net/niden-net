<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {% for url in sitemap %}
    <url>
        <loc>{{ url['location'] }}</loc>
        <lastmod>{{ url['lastModified'] }}</lastmod>
        <changefreq>{{ url['changeFrequency'] }}</changefreq>
        <priority>{{ url['priority'] }}</priority>
    </url>
    {% endfor %}
</urlset>
