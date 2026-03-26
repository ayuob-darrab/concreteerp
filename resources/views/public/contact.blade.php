@extends('layouts.auth')

@php
    $pageHeading = $contactSettings->title ?: 'تواصل معنا';
@endphp

@section('title', $pageHeading . ' - ConcreteERP')

@push('page_meta')
    <meta name="subject" content="{{ $pageHeading }} — ConcreteERP">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "{{ $pageHeading }} - ConcreteERP",
        "description": "طرق التواصل مع فريق ConcreteERP للاستفسار عن الاشتراك أو الدعم أو العروض.",
        "url": "{{ url()->current() }}",
        "inLanguage": "ar"
    }
    </script>
@endpush

@section('content')

<style>
    .auth-container { max-width: 640px; }
    .auth-card { border-radius: 1.5rem; }
    .auth-body { padding: 2rem 2.5rem; background-color: #f8fafc; }
    .contact-box {
        background: #fff; border-radius: 1rem; padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
        text-align: center;
    }
    .contact-welcome { color: #475569; font-size: .98rem; line-height: 1.85; margin: 0 0 1.25rem; }
    .contact-channels {
        display: flex; flex-direction: column; gap: .65rem; margin-top: .5rem;
    }
    .contact-channels a {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .85rem 1rem; border-radius: .85rem; font-weight: 700; text-decoration: none;
        color: #fff; transition: opacity .15s, transform .15s;
    }
    .contact-channels a:hover { opacity: .92; color: #fff; transform: translateY(-1px); }
    .ch-email { background: #475569; }
    .ch-whatsapp { background: #25d366; }
    .ch-telegram { background: #0088cc; }
    .ch-facebook { background: #1877f2; }
    .ch-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
    .ch-phone { background: #0f3460; }
    .contact-value-hint { font-size: .78rem; opacity: .9; font-weight: 500; margin-top: .15rem; }
    .contact-hint { font-size: .82rem; color: #64748b; margin-top: 1.25rem; line-height: 1.7; }
    @media (prefers-color-scheme: dark) {
        .auth-body { background-color: rgba(255,255,255,.04); }
        .contact-box {
            background: rgba(2,6,23,.62) !important;
            border-color: rgba(255,255,255,.14) !important;
        }
        .contact-welcome { color: #cbd5e1 !important; }
        .contact-hint { color: #94a3b8 !important; }
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo"><i class="fas fa-comments"></i></div>
        <h1 class="auth-title">{{ $pageHeading }}</h1>
    </div>

    <div class="auth-body">
        <div class="contact-box">
            @if (filled($contactSettings->intro_text))
                <p class="contact-welcome mb-0">{!! nl2br(e($contactSettings->intro_text)) !!}</p>
            @endif

            @php
                $rows = [
                    ['href' => $contactSettings->emailHref(), 'label' => 'البريد الإلكتروني', 'cls' => 'ch-email', 'icon' => 'fas fa-envelope', 'value' => $contactSettings->email, 'external' => false],
                    ['href' => $contactSettings->whatsappHref(), 'label' => 'واتساب', 'cls' => 'ch-whatsapp', 'icon' => 'fab fa-whatsapp', 'value' => $contactSettings->whatsapp, 'external' => true],
                    ['href' => $contactSettings->telegramHref(), 'label' => 'تيليجرام', 'cls' => 'ch-telegram', 'icon' => 'fab fa-telegram', 'value' => $contactSettings->telegram, 'external' => true],
                    ['href' => $contactSettings->facebookHref(), 'label' => 'فيسبوك', 'cls' => 'ch-facebook', 'icon' => 'fab fa-facebook-f', 'value' => $contactSettings->facebook, 'external' => true],
                    ['href' => $contactSettings->instagramHref(), 'label' => 'إنستغرام', 'cls' => 'ch-instagram', 'icon' => 'fab fa-instagram', 'value' => $contactSettings->instagram, 'external' => true],
                    ['href' => $contactSettings->phoneHref(), 'label' => 'هاتف', 'cls' => 'ch-phone', 'icon' => 'fas fa-phone', 'value' => $contactSettings->phone, 'external' => false],
                ];
                $anyChannel = collect($rows)->contains(fn ($r) => $r['href'] !== null);
            @endphp

            @if ($anyChannel)
                <div class="contact-channels">
                    @foreach ($rows as $r)
                        @continue($r['href'] === null)
                        <div>
                            <a href="{{ $r['href'] }}" class="{{ $r['cls'] }} w-100" @if($r['external']) target="_blank" rel="noopener noreferrer" @endif>
                                <i class="{{ $r['icon'] }}"></i>
                                <span>{{ $r['label'] }}</span>
                            </a>
                            @if (filled($r['value']))
                                <div class="contact-value-hint text-muted">{{ $r['value'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif (! filled($contactSettings->intro_text))
                <p class="text-muted small mb-0">لم تُعرض وسائل تواصل بعد. يمكنك ضبطها من لوحة السوبر أدمن ← صفحات العرض ← تواصل معنا.</p>
            @endif

            @if (filled($contactSettings->hint_text))
                <p class="contact-hint mb-0">{!! nl2br(e($contactSettings->hint_text)) !!}</p>
            @endif
        </div>
    </div>

    <div class="auth-footer">
        <span class="small text-muted">ConcreteERP</span>
    </div>
</div>
@endsection
