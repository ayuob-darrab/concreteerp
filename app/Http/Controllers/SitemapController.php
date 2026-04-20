<?php

namespace App\Http\Controllers;

use App\Models\PageSeoSetting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = 'https://concreteerp.app';
        $lastmod = now()->toDateString();

        $pageSettings = PageSeoSetting::all()->keyBy('page_key');

        $pages = [
            [
                'loc' => $base . '/',
                'priority' => $pageSettings->get('home')?->sitemap_priority ?? '1.0',
                'lastmod' => $lastmod,
                'changefreq' => $pageSettings->get('home')?->sitemap_changefreq ?? 'monthly'
            ],
            [
                'loc' => $base . '/system-benefits',
                'priority' => $pageSettings->get('system-benefits')?->sitemap_priority ?? '0.8',
                'lastmod' => $lastmod,
                'changefreq' => $pageSettings->get('system-benefits')?->sitemap_changefreq ?? 'monthly'
            ],
            [
                'loc' => $base . '/features',
                'priority' => $pageSettings->get('features')?->sitemap_priority ?? '0.8',
                'lastmod' => $lastmod,
                'changefreq' => $pageSettings->get('features')?->sitemap_changefreq ?? 'monthly'
            ],
            [
                'loc' => $base . '/about',
                'priority' => $pageSettings->get('about')?->sitemap_priority ?? '0.8',
                'lastmod' => $lastmod,
                'changefreq' => $pageSettings->get('about')?->sitemap_changefreq ?? 'monthly'
            ],
            [
                'loc' => $base . '/contact',
                'priority' => $pageSettings->get('contact')?->sitemap_priority ?? '0.8',
                'lastmod' => $lastmod,
                'changefreq' => $pageSettings->get('contact')?->sitemap_changefreq ?? 'monthly'
            ],
        ];

        return response()
            ->view('sitemap', compact('pages'), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

