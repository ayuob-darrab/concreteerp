@extends('layouts.auth')

@section('title', 'فوائد نظام ConcreteERP - نظام إدارة مصانع الخرسانة الجاهزة')

@push('page_meta')
    <meta name="subject" content="نظام إدارة مصانع الخرسانة الجاهزة">
    <meta name="classification" content="Business Software">
    <meta name="coverage" content="العراق، الخليج، الشرق الأوسط">
    <meta name="target" content="شركات الخرسانة الجاهزة، مصانع الخرسانة، المقاولين">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "فوائد نظام ConcreteERP",
        "description": "نظام ERP متكامل لإدارة مصانع الخرسانة الجاهزة - الطلبات، الأسطول، المقاولين، المخزون، الشحنات والمحاسبة.",
        "url": "{{ url()->current() }}",
        "inLanguage": "ar",
        "isPartOf": {
            "@type": "WebSite",
            "name": "ConcreteERP",
            "url": "{{ url('/') }}"
        },
        "about": {
            "@type": "SoftwareApplication",
            "name": "ConcreteERP",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "offers": {
                "@type": "Offer",
                "category": "اشتراك شهري / سنوي"
            }
        }
    }
    </script>
@endpush

@section('content')

@php
    $plainBlocks = $displayBlocks->where('block_type', 'plain');
    $kpiBlocks = $displayBlocks->where('block_type', 'kpi')->values();
    $sidebarBlock = $displayBlocks->firstWhere('block_type', 'sidebar');
    $sidebarNote = $displayBlocks->firstWhere('block_type', 'sidebar_note');
    $cardBlocks = $displayBlocks->where('block_type', 'card')->values();
    $footerNote = $displayBlocks->firstWhere('block_type', 'footer_note');
@endphp

<style>
    .auth-container { max-width: 1100px; }
    .auth-card      { border-radius: 1.5rem; }
    .auth-body      { padding: 2rem 2.5rem; background-color: #f8fafc; }
    .benefits-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .hero-text {
        flex: 1 1 520px;
        min-width: 280px;
        background: #fff;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
    }
    .hero-actions {
        flex: 0 0 320px;
        min-width: 260px;
        background: #fff;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: .6rem;
    }
    .page-intro { font-size: .95rem; color: #64748b; margin-top: .5rem; }
    .hero-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: .75rem;
        margin-top: 1rem;
    }
    .kpi {
        background: rgba(52,152,219,.08);
        border: 1px solid rgba(52,152,219,.18);
        border-radius: .9rem;
        padding: .75rem .9rem;
    }
    .kpi strong { display: block; font-size: .95rem; color: #0f172a; }
    .kpi span   { display: block; font-size: .78rem; color: #475569; margin-top: .15rem; }
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .benefit-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        height: 100%;
    }
    .benefit-icon {
        width: 40px; height: 40px;
        border-radius: .75rem;
        display: flex; align-items: center; justify-content: center;
        background: rgba(52,152,219,.08);
        color: #0f3460;
        margin-bottom: .75rem;
        font-size: 1.2rem;
    }
    .benefit-title {
        font-weight: 700;
        margin-bottom: .5rem;
        color: #0f172a;
        font-size: 1rem;
    }
    .benefit-card ul {
        margin: 0; padding-right: 1rem;
        font-size: .9rem; color: #475569;
    }
    .benefit-card ul li + li { margin-top: .25rem; }
    .cta-btn {
        display: inline-flex;
        align-items: center; justify-content: center;
        gap: .5rem; width: 100%;
        padding: .9rem 1rem;
        font-weight: 700;
        border-radius: .75rem;
    }
    .cta-secondary { width: 100%; }
    .hero-actions h6 { font-size: 1rem; font-weight: 800; margin: 0; }
    .benefits-note-box {
        margin-top: .25rem;
        border-radius: .85rem;
        padding: .75rem .9rem;
        font-size: .82rem;
        line-height: 1.75;
        font-weight: 600;
        background: #f0f9ff;
        border: 1px solid #7dd3fc;
        color: #0c4a6e;
    }
    .benefits-note-box--footer {
        margin-top: 1.25rem;
        font-size: .95rem;
        padding: 1rem 1.15rem;
    }
    .benefits-video-wrap {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        background: #000;
        margin-bottom: 1.25rem;
    }
    .benefits-video-wrap .ratio { min-height: 200px; }
    @media (prefers-color-scheme: dark) {
        .auth-body { background-color: rgba(255,255,255,.04); }
        .page-intro, .kpi span, .benefit-card ul, .text-muted { color: #fff !important; }
        .benefit-title, .kpi strong { color: #fff !important; }
        .benefit-card, .hero-actions, .hero-text {
            background: rgba(2,6,23,.62) !important;
            border-color: rgba(255,255,255,.14) !important;
        }
        .kpi {
            background: rgba(56,189,248,.22);
            border-color: rgba(56,189,248,.30);
        }
        .benefits-note-box {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
    }
    @media (max-width: 576px) {
        .auth-body { padding: 1.5rem; }
        .hero-kpis { grid-template-columns: 1fr; }
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo"><i class="fas fa-industry"></i></div>
        <h1 class="auth-title">فوائد نظام ConcreteERP</h1>
        <p class="auth-subtitle">نظام متكامل لإدارة مصانع الخرسانة الجاهزة من الطلب حتى التحصيل</p>
    </div>

    <div class="auth-body">

        @foreach($displayVideos as $video)
            @if($video->embed_url)
                <div class="benefits-video-wrap">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->embed_url }}" title="{{ $video->title ?? 'فيديو تعريفي' }}" allowfullscreen loading="lazy" class="border-0 w-100 h-100"></iframe>
                    </div>
                    @if($video->title)
                        <div class="px-2 py-1 text-center small text-white bg-dark bg-opacity-75">{{ $video->title }}</div>
                    @endif
                </div>
            @endif
        @endforeach

        <div class="benefits-hero">
            <div class="hero-text">
                @forelse($plainBlocks as $p)
                    <div class="{{ !$loop->first ? 'mt-3' : '' }}">
                        @if($p->title)
                            <h3 class="h6 fw-bold text-dark mb-2">{{ $p->title }}</h3>
                        @endif
                        <p class="page-intro {{ !$loop->first ? 'mt-0' : 'mb-2' }}">{!! nl2br(e($p->body)) !!}</p>
                    </div>
                @empty
                    <p class="text-muted small">لم يُضف محتوى بعد — أضف الكتل من لوحة «صفحات العرض».</p>
                @endforelse
                @if($kpiBlocks->isNotEmpty())
                    <div class="hero-kpis">
                        @foreach($kpiBlocks as $k)
                            <div class="kpi">
                                <strong>{{ $k->title }}</strong>
                                <span>{{ $k->body }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="hero-actions">
                @if($sidebarBlock)
                    @if($sidebarBlock->title)
                        <h6 class="fw-bold mb-2">{{ $sidebarBlock->title }}</h6>
                    @endif
                    @if($sidebarBlock->body)
                        <p class="text-muted small mb-0">{!! nl2br(e($sidebarBlock->body)) !!}</p>
                    @endif
                @endif

                @if($whatsappLink)
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-success mt-2 cta-secondary">
                        <i class="fab fa-whatsapp ms-1"></i>
                        مراسلة عبر واتساب
                    </a>
                    @if(optional($ownerCompany)->phone)
                        <div class="text-muted small mt-1">رقم التواصل: <span class="fw-bold">{{ $ownerCompany->phone }}</span></div>
                    @endif
                @endif

                @if($sidebarNote && $sidebarNote->body)
                    <div class="benefits-note-box mt-3 mb-0" role="status">
                        <i class="fas fa-shield-halved me-1"></i>
                        @if($sidebarNote->title)
                            <strong class="d-block mb-1">{{ $sidebarNote->title }}</strong>
                        @endif
                        {!! nl2br(e($sidebarNote->body)) !!}
                    </div>
                @endif
            </div>
        </div>

        @if($cardBlocks->isNotEmpty())
            <div class="benefits-grid">
                @foreach($cardBlocks as $card)
                    <div class="benefit-card">
                        @if($card->icon_fa)
                            <div class="benefit-icon"><i class="fas {{ $card->icon_fa }}"></i></div>
                        @endif
                        @if($card->title)
                            <div class="benefit-title">{{ $card->title }}</div>
                        @endif
                        @if($card->body)
                            <p class="small text-muted mb-2">{{ $card->body }}</p>
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

        @if($footerNote && $footerNote->body)
            <div class="benefits-note-box benefits-note-box--footer" role="note">
                <i class="fas fa-circle-info me-2"></i>
                @if($footerNote->title)
                    <strong class="d-block mb-1">{{ $footerNote->title }}</strong>
                @endif
                {!! nl2br(e($footerNote->body)) !!}
            </div>
        @endif
    </div>

    <div class="auth-footer">
        <span class="small text-muted">ConcreteERP</span>
    </div>
</div>
@endsection
