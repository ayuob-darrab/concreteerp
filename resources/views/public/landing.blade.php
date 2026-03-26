@extends('layouts.auth')

@section('title', 'ConcreteERP — نظام إدارة مصانع الخرسانة الجاهزة')

@push('page_meta')
    <meta name="description" content="ConcreteERP منصة رقمية تساعد مصانع الخرسانة الجاهزة على تنظيم العمل اليومي، توحيد المعلومة، والتصرف بثقة دون الاعتماد على تشتت الأوراق والبرامج المتفرقة.">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "ConcreteERP",
        "url": "{{ url('/') }}",
        "description": "منصة رقمية لدعم الإدارة والتشغيل في قطاع الخرسانة الجاهزة",
        "inLanguage": "ar"
    }
    </script>
@endpush

@section('content')

<style>
    .auth-container { max-width: 920px; }
    /* خلفية الصفحة الداخلية فاتحة وثابتة — نص داكن مقروء في الوضعين الفاتح والداكن */
    .auth-card.is-landing .auth-body {
        padding: 2rem 2.25rem;
        background: #eef2f7;
    }
    .landing-intro {
        font-size: 1.08rem;
        color: #0f172a;
        line-height: 1.9;
        font-weight: 600;
        margin: 0 0 1.25rem;
    }
    .landing-body {
        font-size: 0.97rem;
        color: #1e293b;
        line-height: 1.9;
        margin: 0 0 1rem;
    }
    .landing-body:last-of-type { margin-bottom: 0; }
    .landing-plain-heading {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.5rem;
        line-height: 1.4;
    }
    .landing-bridge-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        margin: 1rem 0 0.35rem;
    }
    .landing-block {
        margin-top: 1.75rem;
        padding: 1.25rem 1.35rem;
        border-radius: 1rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .landing-block h2 {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.85rem;
    }
    .landing-points {
        margin: 0;
        padding-right: 1.2rem;
        color: #1e293b;
        font-size: 0.95rem;
        line-height: 1.85;
    }
    .landing-points strong { color: #0f172a; }
    .landing-points li + li { margin-top: 0.5rem; }
    .landing-bridge {
        font-size: 0.88rem;
        color: #334155;
        margin: 1.25rem 0 0;
        line-height: 1.75;
        font-weight: 500;
    }
    .landing-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1.5rem;
    }
    .landing-actions .btn { border-radius: 0.75rem; font-weight: 700; padding: 0.65rem 1rem; }
    .landing-video-wrap {
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
        background: #000;
    }
    .landing-footer-note {
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
    }
    /* في الوضع الداكن لنظام التشغيل: نفس اللوحة الفاتحة لتفادي نص رمادي على خلفية متدرجة */
    @media (prefers-color-scheme: dark) {
        .auth-card.is-landing .auth-body {
            background: #e8edf3 !important;
        }
        .landing-intro { color: #0f172a; }
        .landing-body { color: #1e293b; }
        .landing-block {
            background: #ffffff;
            border-color: #94a3b8;
        }
        .landing-block h2 { color: #0f172a; }
        .landing-points { color: #1e293b; }
        .landing-points strong { color: #0c1222; }
        .landing-bridge { color: #334155; }
        .landing-plain-heading, .landing-bridge-title { color: #0f172a; }
        .landing-footer-note { color: #475569; }
    }
</style>

<div class="auth-card is-landing">
    <div class="auth-header">
        <div class="auth-logo"><i class="fas fa-industry"></i></div>
        <h1 class="auth-title">ConcreteERP</h1>
        <p class="auth-subtitle mb-0">رفيق رقمي للإدارة والتشغيل في قطاع الخرسانة الجاهزة</p>
    </div>

    <div class="auth-body">
        @foreach($displayVideos as $video)
            @if($video->embed_url)
                <div class="landing-video-wrap">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->embed_url }}" title="{{ $video->title ?? 'فيديو' }}" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin" class="border-0 w-100 h-100"></iframe>
                    </div>
                </div>
            @endif
        @endforeach

        @php $plainIdx = 0; @endphp
        @forelse($displayBlocks as $block)
            @switch($block->block_type)
                @case('plain')
                    @php $plainIdx++; @endphp
                    @if($block->title)
                        <h2 class="landing-plain-heading">{{ $block->title }}</h2>
                    @endif
                    <p class="{{ $plainIdx === 1 ? 'landing-intro' : 'landing-body' }}">{!! nl2br(e($block->body)) !!}</p>
                    @break
                @case('highlight')
                    <div class="landing-block">
                        @if($block->title)<h2>{{ $block->title }}</h2>@endif
                        @if($block->list_items && count($block->list_items))
                            <ul class="landing-points">
                                @foreach($block->list_items as $li)
                                    <li>{!! nl2br(e($li)) !!}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    @break
                @case('bridge')
                    @if($block->title)
                        <p class="landing-bridge-title mb-0">{{ $block->title }}</p>
                    @endif
                    <p class="landing-bridge">{!! nl2br(e($block->body)) !!}</p>
                    @break
            @endswitch
        @empty
            <p class="landing-body">لم يُضف محتوى بعد.</p>
        @endforelse

        <div class="landing-actions">
            <a href="{{ route('login') }}" class="btn btn-login text-white">
                <i class="fas fa-right-to-bracket ms-1"></i> تسجيل الدخول
            </a>
            @if($whatsappLink)
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-success">
                    <i class="fab fa-whatsapp ms-1"></i> واتساب
                </a>
            @endif
        </div>
    </div>

    <div class="auth-footer">
        <span class="landing-footer-note">ConcreteERP — إدارة أوضح ليوم عمل المصنع</span>
    </div>
</div>
@endsection
