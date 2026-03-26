@extends('layouts.auth')

@section('title', 'عن ConcreteERP - نظام إدارة مصانع الخرسانة الجاهزة')

@push('page_meta')
    <meta name="subject" content="عن نظام ConcreteERP">
    <meta name="classification" content="Business Software">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        "name": "عن ConcreteERP",
        "description": "تعرف على فلسفة النظام وأهدافه في دعم مصانع الخرسانة الجاهزة ورقمنة العمليات التشغيلية والمالية.",
        "url": "{{ url()->current() }}",
        "inLanguage": "ar",
        "mainEntity": {
            "@type": "SoftwareApplication",
            "name": "ConcreteERP",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web"
        }
    }
    </script>
@endpush

@section('content')

@php
    $sections = $displayBlocks->where('block_type', 'section')->values();
    $noteBlock = $displayBlocks->firstWhere('block_type', 'note');
@endphp

<style>
    .auth-container { max-width: 900px; }
    .auth-card { border-radius: 1.5rem; }
    .auth-body { padding: 2rem 2.5rem; background-color: #f8fafc; }
    .about-section {
        background: #fff; border-radius: 1rem; padding: 1.35rem 1.5rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
        margin-bottom: 1.25rem;
    }
    .about-section h2 {
        font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: .75rem;
    }
    .about-section p {
        font-size: .95rem; color: #475569; line-height: 1.85; margin: 0 0 .75rem;
    }
    .about-section p:last-child { margin-bottom: 0; }
    .values-list {
        margin: .5rem 0 0; padding-right: 1.2rem; font-size: .92rem; color: #334155; line-height: 1.75;
    }
    .values-list li + li { margin-top: .35rem; }
    .about-note {
        padding: 1rem 1.15rem;
        border-radius: 0.85rem;
        background: #f0f9ff;
        border: 1px solid #7dd3fc;
        color: #0c4a6e;
        font-size: 0.92rem;
        line-height: 1.75;
    }
    @media (prefers-color-scheme: dark) {
        .auth-body { background-color: rgba(255,255,255,.04); }
        .about-section {
            background: rgba(2,6,23,.62) !important;
            border-color: rgba(255,255,255,.14) !important;
        }
        .about-section h2 { color: #fff !important; }
        .about-section p, .values-list { color: #e2e8f0 !important; }
        .about-note {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo"><i class="fas fa-building"></i></div>
        <h1 class="auth-title">عن ConcreteERP</h1>
        <p class="auth-subtitle">منصة رقمية تدعم مصانع الخرسانة الجاهزة في العمليات والجودة والشفافية المالية</p>
    </div>

    <div class="auth-body">
        @forelse($sections as $sec)
            <div class="about-section">
                @if($sec->title)
                    <h2>{{ $sec->title }}</h2>
                @endif
                @if($sec->body)
                    @foreach(preg_split("/\n\s*\n/", trim($sec->body)) as $para)
                        @if(trim($para) !== '')
                            <p>{!! nl2br(e(trim($para))) !!}</p>
                        @endif
                    @endforeach
                @endif
                @if($sec->list_items && count($sec->list_items))
                    <ul class="values-list">
                        @foreach($sec->list_items as $li)
                            <li>{!! nl2br(e($li)) !!}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <p class="text-muted">لم يُضف محتوى بعد.</p>
        @endforelse

        @if($noteBlock && $noteBlock->body)
            <div class="about-note mt-3" role="note">
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
