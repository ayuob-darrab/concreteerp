@extends('layouts.auth')

@section('title', 'مميزات ConcreteERP - إدارة مصانع الخرسانة الجاهزة')

@push('page_meta')
    <meta name="subject" content="مميزات نظام إدارة مصانع الخرسانة الجاهزة">
    <meta name="classification" content="Business Software">
    <meta name="coverage" content="العراق، الخليج، الشرق الأوسط">
    <meta name="target" content="شركات الخرسانة الجاهزة، مصانع الخرسانة، المقاولين">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "مميزات نظام ConcreteERP",
        "description": "استكشف وحدات النظام: الطلبات، الأسطول، المخزون، الخلطات، المقاولين، الحضور، والتقارير المالية في منصة واحدة.",
        "url": "{{ url()->current() }}",
        "inLanguage": "ar",
        "isPartOf": {
            "@type": "WebSite",
            "name": "ConcreteERP",
            "url": "{{ url('/') }}"
        }
    }
    </script>
@endpush

@section('content')

@php
    $heroPlain = $displayBlocks->where('block_type', 'plain');
    $heroSidebar = $displayBlocks->firstWhere('block_type', 'sidebar');
    $featCards = $displayBlocks->where('block_type', 'card')->values();
    $longText = $displayBlocks->firstWhere('block_type', 'long_text');
    $noteBlock = $displayBlocks->firstWhere('block_type', 'note');
@endphp

<style>
    .auth-container { max-width: 1100px; }
    .auth-card { border-radius: 1.5rem; }
    .auth-body { padding: 2rem 2.5rem; background-color: #f8fafc; }
    .features-hero {
        display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: flex-start;
        justify-content: space-between; margin-bottom: 1.25rem;
    }
    .hero-text {
        flex: 1 1 520px; min-width: 280px;
        background: #fff; border-radius: 1rem; padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
    }
    .hero-actions {
        flex: 0 0 300px; min-width: 260px;
        background: #fff; border-radius: 1rem; padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
    }
    .page-intro { font-size: .95rem; color: #64748b; margin-top: .5rem; line-height: 1.75; }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem; margin-top: 1.5rem;
    }
    .feat-card {
        background: #fff; border-radius: 1rem; padding: 1.25rem 1.5rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08); height: 100%;
    }
    .feat-icon {
        width: 42px; height: 42px; border-radius: .75rem;
        display: flex; align-items: center; justify-content: center;
        background: rgba(52,152,219,.1); color: #0f3460; margin-bottom: .75rem;
        font-size: 1.15rem;
    }
    .feat-title { font-weight: 700; color: #0f172a; margin-bottom: .5rem; font-size: 1rem; }
    .feat-card p, .feat-card ul { font-size: .9rem; color: #475569; margin: 0; line-height: 1.7; }
    .feat-card ul { padding-right: 1.1rem; margin-top: .35rem; }
    .feat-card ul li + li { margin-top: .25rem; }
    .cta-btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%; padding: .85rem 1rem; font-weight: 700; border-radius: .75rem; }
    .long-copy-readability {
        margin-top: 1.75rem;
        padding: 1.35rem 1.5rem;
        border-radius: 1rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.07);
    }
    .long-copy-readability h2 {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.75rem;
        line-height: 1.45;
    }
    .long-copy-readability p {
        color: #1e293b;
        font-size: 0.98rem;
        line-height: 1.95;
        font-weight: 500;
        margin: 0 0 1rem;
    }
    .long-copy-readability p:last-child { margin-bottom: 0; }
    .public-page-note {
        margin-top: 1.25rem;
        padding: 1rem 1.15rem;
        border-radius: 0.85rem;
        font-size: 0.92rem;
        line-height: 1.75;
        font-weight: 600;
        background: #f0f9ff;
        border: 1px solid #7dd3fc;
        color: #0c4a6e;
    }
    @media (prefers-color-scheme: dark) {
        .auth-body { background-color: rgba(255,255,255,.04); }
        .hero-text, .hero-actions, .feat-card {
            background: rgba(2,6,23,.62) !important;
            border-color: rgba(255,255,255,.14) !important;
        }
        .page-intro, .feat-card p, .feat-card ul { color: #e2e8f0 !important; }
        .feat-title { color: #fff !important; }
        .long-copy-readability {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
        }
        .long-copy-readability h2 { color: #0f172a !important; }
        .long-copy-readability p { color: #1e293b !important; }
        .public-page-note {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo"><i class="fas fa-layer-group"></i></div>
        <h1 class="auth-title">مميزات نظام ConcreteERP</h1>
        <p class="auth-subtitle">منصة موحّدة تغطي دورة العمل الكاملة في مصنع الخرسانة الجاهزة</p>
    </div>

    <div class="auth-body">
        <div class="features-hero">
            <div class="hero-text">
                @forelse($heroPlain as $p)
                    <div class="{{ !$loop->first ? 'mt-3' : '' }}">
                        @if($p->title)
                            <h3 class="h6 fw-bold mb-2" style="color:#0f172a;">{{ $p->title }}</h3>
                        @endif
                        <p class="page-intro {{ !$loop->first ? 'mt-0' : 'mb-0' }}">{!! nl2br(e($p->body)) !!}</p>
                    </div>
                @empty
                    <p class="text-muted small">لم يُضف نص المقدمة بعد.</p>
                @endforelse
            </div>
            <div class="hero-actions">
                @if($heroSidebar)
                    @if($heroSidebar->title)
                        <h6 class="fw-bold mb-2">{{ $heroSidebar->title }}</h6>
                    @endif
                    @if($heroSidebar->body)
                        <p class="text-muted small mb-3">{!! nl2br(e($heroSidebar->body)) !!}</p>
                    @endif
                @endif
                @if($whatsappLink)
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-success cta-btn">
                        <i class="fab fa-whatsapp ms-1"></i> واتساب
                    </a>
                @endif
            </div>
        </div>

        @if($featCards->isNotEmpty())
            <div class="features-grid">
                @foreach($featCards as $card)
                    <div class="feat-card">
                        @if($card->icon_fa)
                            <div class="feat-icon"><i class="fas {{ $card->icon_fa }}"></i></div>
                        @endif
                        @if($card->title)
                            <div class="feat-title">{{ $card->title }}</div>
                        @endif
                        @if($card->body)
                            <p>{{ $card->body }}</p>
                        @endif
                        @if($card->list_items && count($card->list_items))
                            <ul>
                                @foreach($card->list_items as $li)
                                    <li>{{ $li }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($longText && ($longText->title || $longText->body))
            <div class="long-copy-readability" lang="ar">
                @if($longText->title)
                    <h2>{{ $longText->title }}</h2>
                @endif
                @if($longText->body)
                    @foreach(preg_split("/\n\s*\n/", trim($longText->body)) as $para)
                        @if(trim($para) !== '')
                            <p>{!! nl2br(e(trim($para))) !!}</p>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif

        @if($noteBlock && $noteBlock->body)
            <div class="public-page-note" role="note">
                <i class="fas fa-circle-info me-2"></i>
                @if($noteBlock->title)
                    <strong class="d-block mb-1">{{ $noteBlock->title }}</strong>
                @endif
                {!! nl2br(e($noteBlock->body)) !!}
            </div>
        @endif
    </div>

    <div class="auth-footer">
        <span class="small text-muted">ConcreteERP</span>
    </div>
</div>
@endsection
