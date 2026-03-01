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

        @media (max-width: 576px) {
            .auth-body {
                padding: 1.5rem;
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
            <p class="page-intro">
                يوفر ConcreteERP رؤية كاملة لدورة العمل اليومية في مصنع الخرسانة الجاهزة، ويساعد الإدارة على التحكم في
                الطلبات، التسعير، الأسطول، والنتائج المالية من شاشة واحدة.
            </p>

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
            <a href="{{ route('login') }}">العودة إلى صفحة تسجيل الدخول</a>
        </div>
    </div>
@endsection
