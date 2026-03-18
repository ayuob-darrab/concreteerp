<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($seo) && $seo ? ($seo->meta_title ?? $seo->site_name) : 'تسجيل الدخول') - ConcreteERP</title>

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
            align-items: center;
            justify-content: center;
            padding: 1rem;
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
    <div class="auth-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
