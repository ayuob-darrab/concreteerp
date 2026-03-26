<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PublicContactSettings;
use App\Services\PublicDisplayPageService;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    private function getOwnerCompany()
    {
        return Company::where('code', 'SA')->first();
    }

    public function landing(PublicDisplayPageService $pages)
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        $ownerCompany = $this->getOwnerCompany();
        $displayBlocks = $pages->blocksOrdered('landing');
        $displayVideos = $pages->videosFor('landing');
        $whatsappLink = $pages->whatsappLink();

        return view('public.landing', compact(
            'ownerCompany',
            'displayBlocks',
            'displayVideos',
            'whatsappLink'
        ));
    }

    public function systemBenefits(PublicDisplayPageService $pages)
    {
        $ownerCompany = $this->getOwnerCompany();
        $displayBlocks = $pages->blocksOrdered('system_benefits');
        $displayVideos = $pages->videosFor('system_benefits');
        $whatsappLink = $pages->whatsappLink();

        return view('system-benefits', compact(
            'ownerCompany',
            'displayBlocks',
            'displayVideos',
            'whatsappLink'
        ));
    }

    public function features(PublicDisplayPageService $pages)
    {
        $ownerCompany = $this->getOwnerCompany();
        $displayBlocks = $pages->blocksOrdered('features');
        $whatsappLink = $pages->whatsappLink('مرحباً، أريد معرفة مميزات نظام ConcreteERP.');

        return view('public.features', compact('ownerCompany', 'displayBlocks', 'whatsappLink'));
    }

    public function about(PublicDisplayPageService $pages)
    {
        $ownerCompany = $this->getOwnerCompany();
        $displayBlocks = $pages->blocksOrdered('about');

        return view('public.about', compact('ownerCompany', 'displayBlocks'));
    }

    public function contact()
    {
        $ownerCompany = $this->getOwnerCompany();
        $contactSettings = PublicContactSettings::singleton();

        return view('public.contact', compact('ownerCompany', 'contactSettings'));
    }

    public function sitemap()
    {
        $pages = [
            ['url' => url('/'),                'priority' => '1.0', 'freq' => 'weekly'],
            ['url' => url('/system-benefits'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['url' => url('/features'),        'priority' => '0.8', 'freq' => 'monthly'],
            ['url' => url('/about'),           'priority' => '0.7', 'freq' => 'monthly'],
            ['url' => url('/contact'),         'priority' => '0.6', 'freq' => 'monthly'],
        ];

        $content = view('public.sitemap', compact('pages'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $base = rtrim(config('app.url'), '/');
        $sitemap = $base . '/sitemap.xml';
        $lines = [
            'User-agent: *',
            'Disallow:',
            '',
            'Sitemap: ' . $sitemap,
            '',
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
