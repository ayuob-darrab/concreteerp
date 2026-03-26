<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($seo) && $seo ? ($seo->meta_title ?? $seo->site_name) : 'تسجيل الدخول') - ConcreteERP</title>

    {{-- Favicon للصفحات العامة (بدون تسجيل دخول) حسب اسم الصفحة --}}
    @php
        $publicFav = 'assets/favicons/home.svg';
        if (request()->routeIs('login')) {
            $publicFav = 'assets/favicons/login.svg';
        } elseif (request()->routeIs('contact')) {
            $publicFav = 'assets/favicons/contact.svg';
        } elseif (request()->routeIs('about')) {
            $publicFav = 'assets/favicons/about.svg';
        } elseif (request()->routeIs('features')) {
            $publicFav = 'assets/favicons/features.svg';
        } elseif (request()->routeIs('system-benefits')) {
            $publicFav = 'assets/favicons/benefits.svg';
        } elseif (request()->routeIs('landing')) {
            $publicFav = 'assets/favicons/home.svg';
        }

        // كسر كاش المتصفح للأيقونة (خصوصاً صفحة تسجيل الدخول)
        try {
            $favAbs = public_path($publicFav);
            $favVer = is_file($favAbs) ? (string) filemtime($favAbs) : (string) time();
        } catch (\Throwable $e) {
            $favVer = (string) time();
        }
        $favUrl = asset($publicFav) . '?v=' . $favVer;
    @endphp

    {{-- بعض المتصفحات لا تحترم type="image/x-icon" مع SVG، لذلك نضع أكثر من rel --}}
    <link rel="icon" type="image/svg+xml" href="{{ $favUrl }}">
    <link rel="shortcut icon" href="{{ $favUrl }}">
    <link rel="icon" href="{{ $favUrl }}">

    {{-- SEO (لصفحات auth مثل system-benefits و login) --}}
    @if(isset($seo) && $seo)
        <meta name="description" content="{{ $seo->meta_description }}">
        @if($seo->meta_keywords)<meta name="keywords" content="{{ $seo->meta_keywords }}">@endif
        <meta name="robots" content="{{ $seo->robots ?? 'index, follow' }}">
        <meta name="locale" content="{{ $seo->locale ?? 'ar_IQ' }}">
        @if($seo->canonical_domain)
            <link rel="canonical" href="{{ rtrim($seo->canonical_domain, '/') }}{{ request()->getRequestUri() == '/' ? '' : request()->getRequestUri() }}">
        @endif
        <meta property="og:type" content="{{ $seo->og_type ?? 'website' }}">
        <meta property="og:title" content="{{ $seo->og_title ?? $seo->meta_title ?? $seo->site_name }}">
        <meta property="og:description" content="{{ $seo->og_description ?? $seo->meta_description }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ $seo->site_name }}">
        @if($seo->og_image)<meta property="og:image" content="{{ $seo->og_image }}">@endif
        <meta property="og:locale" content="{{ $seo->locale ?? 'ar_IQ' }}">
        <meta name="twitter:card" content="{{ $seo->twitter_card ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo->og_title ?? $seo->meta_title ?? $seo->site_name }}">
        <meta name="twitter:description" content="{{ $seo->og_description ?? $seo->meta_description }}">
        @if($seo->og_image)<meta name="twitter:image" content="{{ $seo->og_image }}">@endif
        @if($seo->twitter_site)<meta name="twitter:site" content="{{ $seo->twitter_site }}">@endif
        @if($seo->extra_meta){!! $seo->extra_meta !!}@endif
        @if($seo->structured_data)<script type="application/ld+json">{!! $seo->structured_data !!}</script>@endif
    @endif

    @stack('page_meta')

    <!-- Fonts (من إعدادات النظام، الافتراضي Cairo) -->
    @php
        $authFontFamily = $app_font_family ?? 'Cairo';
        $authFontSize = $app_font_size ?? '14';
        $authFontParam = str_replace(' ', '+', $authFontFamily);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $authFontParam }}:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: '{{ $authFontFamily }}', sans-serif;
            font-size: {{ $authFontSize }}px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 0.75rem 1rem 1rem;
            gap: 0.75rem;
        }

        /* شريط تنقل الصفحات التعريفية العامة */
        .public-site-nav {
            width: 100%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.85rem;
            padding: 0.5rem 0.85rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.35rem 0.5rem;
            backdrop-filter: blur(8px);
        }

        .public-site-nav a {
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.4rem 0.65rem;
            border-radius: 0.5rem;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }

        .public-site-nav a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .public-site-nav a.is-active {
            background: rgba(52, 152, 219, 0.35);
            color: #fff;
        }

        .public-site-nav .nav-sep {
            width: 1px;
            height: 1rem;
            background: rgba(255, 255, 255, 0.2);
            display: none;
        }

        @media (min-width: 768px) {
            .public-site-nav .nav-sep { display: block; }
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }

        .auth-logo {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .auth-logo img {
            width: 50px;
        }

        .auth-logo i {
            font-size: 2.5rem;
            color: var(--primary);
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating>.form-control {
            padding-right: 3rem;
        }

        .form-floating>label {
            right: 0;
            left: auto;
            padding-right: 3rem;
        }

        .form-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        }

        .auth-footer {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .input-group-text {
            background: transparent;
            border-left: none;
        }

        .form-control:focus+.input-group-text {
            border-color: #86b7fe;
        }

        @media (max-width: 480px) {
            .auth-header {
                padding: 1.5rem;
            }

            .auth-body {
                padding: 1.5rem;
            }

            .auth-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="public-site-nav" aria-label="التنقل الرئيسي">
        <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'is-active' : '' }}">الرئيسية</a>
        <span class="nav-sep" aria-hidden="true"></span>
        <a href="{{ route('system-benefits') }}" class="{{ request()->routeIs('system-benefits') ? 'is-active' : '' }}">فوائد النظام</a>
        <span class="nav-sep" aria-hidden="true"></span>
        <a href="{{ route('features') }}" class="{{ request()->routeIs('features') ? 'is-active' : '' }}">المميزات</a>
        <span class="nav-sep" aria-hidden="true"></span>
        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">عن النظام</a>
        <span class="nav-sep" aria-hidden="true"></span>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}">تواصل معنا</a>
        <span class="nav-sep" aria-hidden="true"></span>
        <a href="{{ route('login') }}"><i class="fas fa-right-to-bracket ms-1"></i> تسجيل الدخول</a>
    </nav>

    <div class="auth-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
