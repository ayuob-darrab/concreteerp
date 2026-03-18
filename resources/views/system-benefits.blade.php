@extends('layouts.auth')

@section('title', 'فوائد نظام ConcreteERP')

@section('content')
    <style>
        /* توسيع العرض ليغطي معظم الشاشة */
        .auth-container {
            max-width: 1100px;
        }

        .auth-card {
            border-radius: 1.5rem;
        }

        .auth-body {
            padding: 2rem 2.5rem;
            background-color: #f8fafc;
        }

        .benefits-hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.25rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .benefits-hero .hero-text {
            flex: 1 1 520px;
            min-width: 280px;
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .benefits-hero .hero-actions {
            flex: 0 0 320px;
            min-width: 260px;
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.6rem;
        }

        .hero-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .kpi {
            background: rgba(52, 152, 219, 0.08);
            border: 1px solid rgba(52, 152, 219, 0.18);
            border-radius: 0.9rem;
            padding: 0.75rem 0.9rem;
        }

        .kpi strong {
            display: block;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .kpi span {
            display: block;
            font-size: 0.78rem;
            color: #475569;
            margin-top: 0.15rem;
        }

        .hero-fill {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0.75rem;
        }

        .hero-box {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .hero-box-title {
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 0.5rem;
        }

        .hero-list {
            margin: 0;
            padding-right: 1rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .hero-list li + li {
            margin-top: 0.25rem;
        }

        .hero-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-top: 0.6rem;
        }

        .chip {
            border: 1px solid rgba(15, 23, 42, 0.10);
            color: #0f172a;
            background: rgba(148, 163, 184, 0.10);
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .benefit-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            height: 100%;
        }

        .benefit-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(52, 152, 219, 0.08);
            color: #0f3460;
            margin-bottom: 0.75rem;
            font-size: 1.2rem;
        }

        .benefit-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #0f172a;
            font-size: 1rem;
        }

        .benefit-card ul {
            margin: 0;
            padding-right: 1rem;
            font-size: 0.9rem;
            color: #475569;
        }

        .benefit-card ul li + li {
            margin-top: 0.25rem;
        }

        .page-intro {
            font-size: 0.95rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.9rem 1rem;
            font-weight: 700;
            border-radius: 0.75rem;
        }

        .cta-secondary {
            width: 100%;
        }

        .hero-actions h6 {
            font-size: 1rem;
            font-weight: 800;
            margin: 0;
        }

        .hero-actions p.text-muted {
            margin: 0;
            font-size: 0.9rem;
        }

        .hero-actions .btn-login {
            border-radius: 0.85rem;
        }

        .hero-actions .btn-outline-success {
            border-radius: 0.85rem;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }

        .hero-actions .alert-info {
            margin-top: 0.25rem;
            border-radius: 0.85rem;
        }

        /* Dark mode: اجعل لون الخط أبيض */
        @media (prefers-color-scheme: dark) {
            .auth-body {
                background-color: rgba(255, 255, 255, 0.04);
            }

            .page-intro,
            .kpi span,
            .hero-list,
            .benefit-card ul,
            .text-muted,
            .alert-info {
                color: #ffffff !important;
            }

            .benefit-title,
            .kpi strong,
            .hero-box-title {
                color: #ffffff !important;
            }

            .benefit-card,
            .hero-box,
            .benefits-hero .hero-actions,
            .benefits-hero .hero-text {
                background: rgba(2, 6, 23, 0.62) !important;
                border-color: rgba(255, 255, 255, 0.14) !important;
            }

            .benefits-hero .hero-text .page-intro,
            .benefits-hero .hero-text .hero-kpis {
                background: transparent !important;
            }

            .kpi {
                background: rgba(56, 189, 248, 0.22);
                border-color: rgba(56, 189, 248, 0.30);
            }

            .hero-actions .alert-info {
                background: rgba(59, 130, 246, 0.16) !important;
                border-color: rgba(59, 130, 246, 0.25) !important;
            }

            .chip {
                color: #ffffff;
                border-color: rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.08);
            }
        }

        @media (max-width: 1024px) {
            .benefits-hero {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 576px) {
            .auth-body {
                padding: 1.5rem;
            }

            .hero-kpis {
                grid-template-columns: 1fr;
            }

            .hero-fill {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fas fa-industry"></i>
            </div>
            <h1 class="auth-title">فوائد نظام ConcreteERP</h1>
            <p class="auth-subtitle">نظام متكامل لإدارة مصانع الخرسانة الجاهزة من الطلب حتى التحصيل</p>
        </div>

        <div class="auth-body">
            <div class="benefits-hero">
                <div class="hero-text">
                    <p class="page-intro mb-2">
                        يوفر ConcreteERP رؤية كاملة لدورة العمل اليومية في مصنع الخرسانة الجاهزة، ويساعد الإدارة على التحكم في
                        الطلبات، التسعير، الأسطول، والنتائج المالية من شاشة واحدة.
                    </p>
                    <div class="hero-kpis">
                        <div class="kpi">
                            <strong>إدارة الطلبات</strong>
                            <span>من أول اتصال حتى الفاتورة والتحصيل</span>
                        </div>
                        <div class="kpi">
                            <strong>الشحنات والأسطول</strong>
                            <span>تتبع التنفيذ والرحلات والجاهزية</span>
                        </div>
                        <div class="kpi">
                            <strong>تقارير فورية</strong>
                            <span>لوحات مؤشرات تساعد القرار</span>
                        </div>
                    </div>

                </div>

                <div class="hero-actions">
                    <h6 class="fw-bold mb-2">الدخول إلى النظام</h6>
                    <p class="text-muted small mb-3">إذا لديك حساب، سجل دخولك للوصول إلى لوحة التحكم.</p>
                    <a href="{{ route('login') }}" class="btn btn-login cta-btn">
                        <i class="fas fa-right-to-bracket"></i>
                        تسجيل الدخول
                    </a>
                    @php
                        $ownerWhatsappRaw = $ownerCompany->phone ?? '';
                        $ownerWhatsapp = preg_replace('/[^0-9]/', '', $ownerWhatsappRaw);

                        // تحويل رقم عراقي محلي مثل 077xxxxxxxx إلى دولي 96477xxxxxxxx (مطلوب لـ wa.me)
                        if ($ownerWhatsapp && str_starts_with($ownerWhatsapp, '0') && strlen($ownerWhatsapp) === 11) {
                            $ownerWhatsapp = '964' . substr($ownerWhatsapp, 1);
                        }
                        $waText = rawurlencode('مرحباً، أريد الاستفسار عن نظام ConcreteERP.');
                        $waLink = $ownerWhatsapp ? ('https://wa.me/' . $ownerWhatsapp . '?text=' . $waText) : null;
                    @endphp
                    @if ($waLink)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener"
                            class="btn btn-success mt-2 cta-secondary">
                            <i class="fab fa-whatsapp ms-1"></i>
                            للاستفسار، تواصل معنا عبر واتساب فريقنا بانتظارك                        </a>
                        <div class="text-muted small mt-1">
                            رقم التواصل: <span class="fw-bold">{{ $ownerCompany->phone }}</span>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary mt-2 cta-secondary">
                            <i class="fas fa-circle-info ms-1"></i>
                            لا يوجد رقم واتساب للشركة المالكة — تواصل عبر تسجيل الدخول
                        </a>
                    @endif
                    <div class="alert alert-info mt-3 mb-0 small" role="alert">
                        <i class="fas fa-shield-halved me-1"></i>
                        الوصول للبيانات يتطلب تسجيل دخول لضمان الأمان والصلاحيات.
                    </div>
                </div>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="benefit-title">١. إدارة الطلبات من أول اتصال حتى الفاتورة</div>
                    <ul>
                        <li>تسجيل طلبات العملاء مع كل التفاصيل (الموقع، الكميات، أوقات الصب).</li>
                        <li>متابعة حالة الطلب: جديد، قيد التفاوض، معتمد، قيد التنفيذ، مكتمل.</li>
                        <li>ربط الطلبات بالفواتير، المدفوعات، والتقارير المالية.</li>
                    </ul>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="benefit-title">٢. تحكم كامل في الأسطول والشحنات</div>
                    <ul>
                        <li>إدارة سيارات النقل والمضخات وربطها بأوامر العمل.</li>
                        <li>متابعة حالة كل شحنة وزمن خروجها ووصولها ومكانها.</li>
                        <li>تقارير عن استغلال الأسطول، الأعطال، والصيانة.</li>
                    </ul>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="benefit-title">٣. تسعير مرن وشفاف</div>
                    <ul>
                        <li>تعريف فئات سعرية مختلفة حسب العميل أو المشروع أو المنطقة.</li>
                        <li>إدارة خصومات واتفاقيات خاصة مع المقاولين والعملاء الرئيسيين.</li>
                        <li>تقارير ربحية على مستوى العميل أو المشروع أو الفرع.</li>
                    </ul>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="benefit-title">٤. صلاحيات واضحة لكل نوع مستخدم</div>
                    <ul>
                        <li>صلاحيات مخصصة للسوبر أدمن، إدارة الشركة، مدير الفرع، الموظفين، والسائقين.</li>
                        <li>سجل نشاط للمستخدمين وعمليات الدخول والتعديلات المهمة.</li>
                        <li>التحكم في تفعيل/إيقاف الشركات والمستخدمين حسب حالة الاشتراك.</li>
                    </ul>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="benefit-title">٥. تقارير ولوحات تحكم للإدارة العليا</div>
                    <ul>
                        <li>تقارير مالية يومية وشهرية عن المبيعات، المقبوضات، والذمم.</li>
                        <li>إحصائيات عن الطلبات، العملاء، الفروع، والأسطول في لوحة واحدة.</li>
                        <li>مؤشرات أداء رئيسية تساعد في اتخاذ القرار بسرعة.</li>
                    </ul>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="benefit-title">٦. دعم فني ومتابعة تشغيلية</div>
                    <ul>
                        <li>نظام تذاكر دعم لتوثيق المشاكل والطلبات من المستخدمين.</li>
                        <li>سجل للأخطاء وحالة النظام لمتابعة الاستقرار والأداء.</li>
                        <li>متابعة حالة اشتراك كل شركة وتنبيهات قبل انتهاء الاشتراك.</li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-circle-info me-2"></i>
                تم تصميم ConcreteERP خصيصًا لمصانع الخرسانة الجاهزة، ليتوافق مع دورة العمل الحقيقية في الميدان
                وليس كنظام محاسبي عام فقط.
            </div>
        </div>

        <div class="auth-footer">
            <a href="{{ route('login') }}" class="fw-bold">
                <i class="fas fa-right-to-bracket ms-1"></i>
                تسجيل الدخول
            </a>
        </div>
    </div>
@endsection
