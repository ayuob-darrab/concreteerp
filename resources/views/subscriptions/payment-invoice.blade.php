<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفعة - {{ $company->name }}</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 30px 40px;
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
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
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
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .invoice-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .invoice-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            backdrop-filter: blur(10px);
        }

        .invoice-body {
            padding: 30px 40px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-card h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 700;
            color: #059669;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6b7280;
            font-weight: 500;
            font-size: 14px;
        }

        .info-value {
            color: #111827;
            font-weight: 600;
            font-size: 14px;
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

        .amount-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin: 30px 0;
        }

        .amount-box {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            text-align: center;
        }

        .amount-box.total {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #3b82f6;
        }

        .amount-box.paid {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #059669;
        }

        .amount-box.remaining {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: #f59e0b;
        }

        .amount-box.remaining.zero {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #059669;
        }

        .amount-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .amount-box.total .amount-label {
            color: #3b82f6;
        }

        .amount-box.paid .amount-label {
            color: #059669;
        }

        .amount-box.remaining .amount-label {
            color: #f59e0b;
        }

        .amount-box.remaining.zero .amount-label {
            color: #059669;
        }

        .amount-value {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .amount-box.total .amount-value {
            color: #3b82f6;
        }

        .amount-box.paid .amount-value {
            color: #059669;
        }

        .amount-box.remaining .amount-value {
            color: #f59e0b;
        }

        .amount-box.remaining.zero .amount-value {
            color: #059669;
        }

        .amount-currency {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table th {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 14px 12px;
            text-align: right;
            font-weight: 700;
            font-size: 14px;
        }

        .details-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            background: white;
            font-size: 14px;
        }

        .details-table tbody tr:last-child td {
            border-bottom: none;
        }

        .footer-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #e5e7eb;
            text-align: center;
            color: #6b7280;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 25px 0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-print {
            background: #059669;
            color: white;
        }

        .btn-print:hover {
            background: #047857;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .stamp {
            width: 120px;
            height: 120px;
            border: 3px solid #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 25px auto;
            transform: rotate(-15deg);
            opacity: 0.15;
            font-size: 20px;
            font-weight: 700;
            color: #059669;
        }

        .divider-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #059669, transparent);
            margin: 20px 0;
        }

        .receipt-badge {
            display: inline-block;
            background: #059669;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .payment-method-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
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
                        <svg width="50" height="50" viewBox="0 0 24 24" fill="#059669">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            <path d="M10 17l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" fill="white" />
                        </svg>
                    @endif
                </div>

                <!-- معلومات الإيصال -->
                <div class="header-info">
                    <div class="invoice-number">
                        رقم الإيصال: RCP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                    <h1>إيصال دفعة</h1>
                    <p>نظام ConcreteERP لإدارة شركات الخرسانة</p>
                </div>

                <!-- أيقونة الدفع -->
                <div class="company-logo">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="#059669">
                        <path
                            d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- رقم الإيصال -->
            <div style="text-align: center; margin-bottom: 20px;">
                <span class="receipt-badge">إيصال دفعة اشتراك</span>
            </div>

            <div class="divider-line"></div>

            <!-- أزرار الطباعة والرجوع -->
            <div class="btn-container no-print">
                <button onclick="window.print()" class="btn btn-print">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    طباعة الإيصال
                </button>
                <a href="{{ route('subscriptions.history', $company->code) }}" class="btn btn-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
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

                <!-- معلومات الدفعة -->
                <div class="info-card">
                    <h3>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                        </svg>
                        تفاصيل الدفعة
                    </h3>
                    <div class="info-row">
                        <span class="info-label">رقم الإيصال:</span>
                        <span class="info-value">#{{ $payment->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الدفع:</span>
                        <span class="info-value">{{ $payment->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">وقت الدفع:</span>
                        <span class="info-value">{{ $payment->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">طريقة الدفع:</span>
                        <span class="info-value">
                            @php
                                $methodLabels = [
                                    'cash' => '💵 نقدي',
                                    'bank_transfer' => '🏦 تحويل بنكي',
                                    'check' => '📝 شيك',
                                    'online' => '💳 إلكتروني',
                                ];
                            @endphp
                            <span class="payment-method-icon">
                                {{ $methodLabels[$payment->payment_method] ?? ($payment->payment_method ?? 'غير محدد') }}
                            </span>
                        </span>
                    </div>
                    @if ($payment->payment_reference)
                        <div class="info-row">
                            <span class="info-label">المرجع:</span>
                            <span class="info-value">{{ $payment->payment_reference }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- مبلغ الدفعة -->
            @php
                $totalAmount = $subscription->base_fee ?? 0;
                $paidAmount = $payment->paid_amount ?? ($payment->base_fee ?? 0);
                $remainingAmount = max(0, $totalAmount - $totalPaid);
            @endphp
            <div class="amount-section">
                <!-- المبلغ الإجمالي -->
                <div class="amount-box total">
                    <div class="amount-label">💰 المبلغ الإجمالي</div>
                    <div class="amount-value">{{ number_format($totalAmount, 0) }}</div>
                    <div class="amount-currency">دينار عراقي</div>
                </div>

                <!-- المبلغ المدفوع -->
                <div class="amount-box paid">
                    <div class="amount-label">✅ هذه الدفعة</div>
                    <div class="amount-value">{{ number_format($paidAmount, 0) }}</div>
                    <div class="amount-currency">دينار عراقي</div>
                </div>

                <!-- المبلغ المتبقي -->
                <div class="amount-box remaining {{ $remainingAmount <= 0 ? 'zero' : '' }}">
                    <div class="amount-label">{{ $remainingAmount <= 0 ? '✓ مسدد بالكامل' : '⏳ المتبقي' }}</div>
                    <div class="amount-value">{{ number_format($remainingAmount, 0) }}</div>
                    <div class="amount-currency">دينار عراقي</div>
                </div>
            </div>

            <!-- تفاصيل إضافية -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th>البيان</th>
                        <th style="text-align: left;">التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>نوع العملية</strong></td>
                        <td style="text-align: left;">
                            <span class="badge badge-success">دفعة اشتراك</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>نوع الخطة</strong></td>
                        <td style="text-align: left;">
                            @php
                                $planLabels = [
                                    'monthly' => 'شهري',
                                    'yearly' => 'سنوي',
                                    'percentage' => 'نسبة من الطلبات',
                                    'trial' => 'تجريبي',
                                    'hybrid' => 'هجين',
                                ];
                            @endphp
                            {{ $planLabels[$payment->plan_type] ?? $payment->plan_type }}
                        </td>
                    </tr>
                    @if ($payment->start_date)
                        <tr>
                            <td><strong>فترة الاشتراك</strong></td>
                            <td style="text-align: left;">
                                {{ $payment->start_date?->format('Y/m/d') }}
                                @if ($payment->end_date)
                                    — {{ $payment->end_date->format('Y/m/d') }}
                                @else
                                    — مفتوح
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if ($payment->notes)
                        <tr>
                            <td><strong>ملاحظات</strong></td>
                            <td style="text-align: left;">{{ $payment->notes }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

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

            <!-- ختم -->
            <div class="stamp">
                مدفوع ✓
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
</body>

</html>
