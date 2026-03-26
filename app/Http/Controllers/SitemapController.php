<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim((string) config('app.url'), '/');
        $lastmod = now()->toDateString();

        $pages = [
            ['loc' => $base . '/',               'priority' => '1.0', 'lastmod' => $lastmod, 'changefreq' => 'weekly'],
            ['loc' => $base . '/system-benefits','priority' => '0.9', 'lastmod' => $lastmod, 'changefreq' => 'weekly'],
            ['loc' => $base . '/features',       'priority' => '0.9', 'lastmod' => $lastmod, 'changefreq' => 'weekly'],
            ['loc' => $base . '/about',          'priority' => '0.8', 'lastmod' => $lastmod, 'changefreq' => 'monthly'],
            ['loc' => $base . '/contact',        'priority' => '0.8', 'lastmod' => $lastmod, 'changefreq' => 'monthly'],
        ];

        return response()
            ->view('sitemap', compact('pages'), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

