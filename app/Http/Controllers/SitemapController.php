<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = 'https://concreteerp.app';
        $lastmod = now()->toDateString();

        $pages = [
            ['loc' => $base . '/',               'priority' => '1.0', 'lastmod' => $lastmod],
            ['loc' => $base . '/system-benefits','priority' => '0.9', 'lastmod' => $lastmod],
            ['loc' => $base . '/features',       'priority' => '0.8', 'lastmod' => $lastmod],
            ['loc' => $base . '/about',          'priority' => '0.7', 'lastmod' => $lastmod],
            ['loc' => $base . '/contact',        'priority' => '0.6', 'lastmod' => $lastmod],
        ];

        return response()
            ->view('sitemap', compact('pages'), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

