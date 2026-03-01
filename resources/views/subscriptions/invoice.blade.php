<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة اشتراك - {{ $company->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .company-logo {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .company-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .header-info {
            flex: 1;
            text-align: center;
        }

        .invoice-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .invoice-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .invoice-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }

        .invoice-body {
            padding: 40px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-card {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }

        .info-card h3 {
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6b7280;
            font-weight: 500;
        }

        .info-value {
            color: #111827;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 15px;
            text-align: right;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .details-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            background: white;
        }

        .details-table tbody tr:hover {
            background: #f9fafb;
        }

        .details-table tbody tr:last-child td {
            border-bottom: none;
        }

        .total-section {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
            border: 3px solid #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 18px;
            color: #374151;
        }

        .total-row.grand-total {
            border-top: 3px solid #667eea;
            padding-top: 25px;
            margin-top: 20px;
            font-size: 28px;
            font-weight: 800;
            color: #667eea;
            text-shadow: 1px 1px 2px rgba(102, 126, 234, 0.3);
        }

        .footer-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px dashed #e5e7eb;
            text-align: center;
            color: #6b7280;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #667eea;
            color: white;
        }

        .btn-print:hover {
            background: #5568d3;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .stamp {
            width: 150px;
            height: 150px;
            border: 4px solid #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px auto;
            transform: rotate(-15deg);
            opacity: 0.15;
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .divider-line {
            height: 3px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 30px 0;
        }

        .invoice-id-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .signatures-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 40px 0 30px;
            padding-top: 30px;
            border-top: 2px dashed #e5e7eb;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box h4 {
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }

        .signature-box .company-name {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 2px solid #374151;
            width: 80%;
            margin: 0 auto;
            position: relative;
        }

        .signature-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="header-content">
                <!-- لوجو الشركة المالكة -->
                <div class="company-logo">
                    @php
                        $ownerCompany = \App\Models\Company::where('code', 'SA')->first();
                    @endphp
                    @if ($ownerCompany && $ownerCompany->logo)
                        <img src="{{ asset($ownerCompany->logo) }}" alt="ConcreteERP Logo">
                    @else
                        <svg width="70" height="70" viewBox="0 0 24 24" fill="#667eea">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            <path d="M10 17l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" fill="white" />
                        </svg>
                    @endif
                </div>

                <!-- معلومات الفاتورة -->
                <div class="header-info">
                    <div class="invoice-number">
                        رقم الفاتورة: INV-{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                    <h1>فاتورة اشتراك</h1>
                    <p>نظام ConcreteERP لإدارة شركات الخرسانة</p>
                    <p style="font-size: 14px; margin-top: 5px;">{{ now()->format('Y/m/d') }}</p>
                </div>

                <!-- شعار أو ختم -->
                <div class="company-logo">
                    <svg width="70" height="70" viewBox="0 0 24 24" fill="white">
                        <path
                            d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- رقم الفاتورة المميز -->
            <div style="text-align: center; margin-bottom: 30px;">
                <span class="invoice-id-badge">الاشتراك رقم: {{ $subscription->id }}</span>
            </div>

            <div class="divider-line"></div>

            <!-- أزرار الطباعة والرجوع -->
            <div class="btn-container no-print">
                <button onclick="window.print()" class="btn btn-print">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    طباعة الفاتورة
                </button>
                <a href="{{ route('subscriptions.companies') }}" class="btn btn-back">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"></path>
                    </svg>
                    رجوع
                </a>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="info-section">
                <!-- معلومات الشركة -->
                <div class="info-card">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                        </svg>
                        معلومات الشركة
                    </h3>
                    <div class="info-row">
                        <span class="info-label">اسم الشركة:</span>
                        <span class="info-value">{{ $company->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">كود الشركة:</span>
                        <span class="info-value">{{ $company->code }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">البريد الإلكتروني:</span>
                        <span class="info-value">{{ $company->email ?? 'غير محدد' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الهاتف:</span>
                        <span class="info-value">{{ $company->phone ?? 'غير محدد' }}</span>
                    </div>
                </div>

                <!-- معلومات الاشتراك -->
                <div class="info-card">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                        </svg>
                        تفاصيل الاشتراك
                    </h3>
                    <div class="info-row">
                        <span class="info-label">رقم الاشتراك:</span>
                        <span class="info-value">#{{ $subscription->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الإصدار:</span>
                        <span
                            class="info-value">{{ \Carbon\Carbon::parse($subscription->created_at)->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الحالة:</span>
                        <span class="info-value">
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    'suspended' => 'warning',
                                ];
                                $statusLabels = [
                                    'active' => 'نشط ✓',
                                    'expired' => 'منتهي ✗',
                                    'suspended' => 'معلق ⚠',
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$subscription->status] ?? 'secondary' }}">
                                {{ $statusLabels[$subscription->status] ?? $subscription->status }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- تفاصيل الخطة -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th>البيان</th>
                        <th style="text-align: center;">التفاصيل</th>
                        <th style="text-align: left;">القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>نوع الخطة</strong></td>
                        <td style="text-align: center;">
                            @php
                                $planLabels = [
                                    'monthly' => 'شهري 📅',
                                    'yearly' => 'سنوي 🗓️',
                                    'percentage' => 'نسبة من الطلبات 📊',
                                    'trial' => 'تجريبي 🧪',
                                    'hybrid' => 'هجين (رسوم + نسبة) 🔄',
                                ];
                            @endphp
                            {{ $planLabels[$subscription->plan_type] ?? $subscription->plan_type }}
                        </td>
                        <td style="text-align: left;">-</td>
                    </tr>

                    @if ($subscription->base_fee)
                        <tr>
                            <td><strong>الرسوم الأساسية</strong></td>
                            <td style="text-align: center;">
                                @if ($subscription->plan_type === 'monthly')
                                    رسوم شهرية ثابتة
                                @elseif($subscription->plan_type === 'yearly')
                                    رسوم سنوية ثابتة
                                @else
                                    رسوم ثابتة
                                @endif
                            </td>
                            <td style="text-align: left;"><strong>{{ number_format($subscription->base_fee, 2) }}
                                    دينار</strong></td>
                        </tr>
                    @endif

                    @if ($subscription->percentage_rate)
                        <tr>
                            <td><strong>نسبة من الطلبات</strong></td>
                            <td style="text-align: center;">نسبة مئوية من كل طلب</td>
                            <td style="text-align: left;"><strong>{{ $subscription->percentage_rate }}%</strong></td>
                        </tr>
                    @endif

                    <tr>
                        <td><strong>تاريخ البداية</strong></td>
                        <td style="text-align: center;">بداية صلاحية الاشتراك</td>
                        <td style="text-align: left;">
                            {{ \Carbon\Carbon::parse($subscription->start_date)->format('Y/m/d') }}</td>
                    </tr>

                    <tr>
                        <td><strong>تاريخ النهاية</strong></td>
                        <td style="text-align: center;">انتهاء صلاحية الاشتراك</td>
                        <td style="text-align: left;">
                            {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y/m/d') : 'مفتوح ∞' }}
                        </td>
                    </tr>

                    @if ($subscription->orders_limit)
                        <tr>
                            <td><strong>حد الطلبات</strong></td>
                            <td style="text-align: center;">الحد الأقصى للطلبات المسموح بها</td>
                            <td style="text-align: left;">{{ number_format($subscription->orders_limit) }} طلب</td>
                        </tr>
                    @endif

                    <tr>
                        <td><strong>الطلبات المستخدمة</strong></td>
                        <td style="text-align: center;">عدد الطلبات المنفذة حتى الآن</td>
                        <td style="text-align: left;">{{ number_format($subscription->orders_used ?? 0) }} طلب</td>
                    </tr>

                    <tr>
                        <td><strong>التجديد التلقائي</strong></td>
                        <td style="text-align: center;">تجديد الاشتراك تلقائياً</td>
                        <td style="text-align: left;">
                            {{ $subscription->auto_renew ? '✓ مفعّل' : '✗ غير مفعّل' }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- القيمة الإجمالية -->
            @if ($subscription->base_fee)
                <div class="total-section">
                    <div class="total-row">
                        <span>الرسوم الأساسية:</span>
                        <span>{{ number_format($subscription->base_fee, 2) }} دينار</span>
                    </div>
                    @if ($subscription->percentage_rate)
                        <div class="total-row">
                            <span>نسبة الطلبات:</span>
                            <span>{{ $subscription->percentage_rate }}% من قيمة كل طلب</span>
                        </div>
                    @endif
                    <div class="total-row grand-total">
                        <span>إجمالي الاشتراك:</span>
                        <span>{{ number_format($subscription->base_fee, 2) }} دينار</span>
                    </div>
                    @if ($subscription->plan_type === 'monthly')
                        <p style="text-align: center; margin-top: 15px; color: #6b7280; font-size: 14px;">
                            * يتم دفع هذا المبلغ شهرياً
                        </p>
                    @elseif($subscription->plan_type === 'yearly')
                        <p style="text-align: center; margin-top: 15px; color: #6b7280; font-size: 14px;">
                            * يتم دفع هذا المبلغ سنوياً
                        </p>
                    @endif
                </div>
            @endif

            <!-- ملاحظات -->
            @if ($subscription->notes)
                <div class="info-card" style="margin-top: 30px;">
                    <h3>📝 ملاحظات</h3>
                    <p style="color: #4b5563; line-height: 1.8;">{{ $subscription->notes }}</p>
                </div>
            @endif

            <!-- ختم الشركة -->
            <div class="stamp">
                ConcreteERP
            </div>

            <!-- قسم التوقيعات -->
            <div class="signatures-section">
                <!-- توقيع الشركة المالكة -->
                <div class="signature-box">
                    <h4>توقيع الشركة</h4>
                    <div class="company-name">{{ $ownerCompany->name ?? 'ConcreteERP' }}</div>
                    <div class="signature-line"></div>
                    <div class="signature-label">التوقيع والختم</div>
                </div>

                <!-- توقيع المشترك -->
                <div class="signature-box">
                    <h4>توقيع المشترك</h4>
                    <div class="company-name">{{ $company->name }}</div>
                    <div class="signature-line"></div>
                    <div class="signature-label">التوقيع والختم</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <p style="margin-bottom: 8px; font-size: 13px;">
                    <strong>شكراً لتعاملكم معنا</strong>
                </p>
                <p style="font-size: 11px;">
                    تاريخ الطباعة: {{ now()->format('Y/m/d') }}
                </p>
                <p style="font-size: 11px; margin-top: 8px;">
                    للاستفسارات: {{ $ownerCompany->phone ?? '07XX XXX XXXX' }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // فتح نافذة الطباعة تلقائياً عند التحميل (اختياري)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>
