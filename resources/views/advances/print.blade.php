<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلفة رقم {{ $advance->advance_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            padding: 15px;
            font-size: 13px;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 3px solid #1a5276;
            padding: 0;
        }

        /* Header with Logo */
        .header {
            background: linear-gradient(135deg, #1a5276 0%, #2980b9 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-info h1 {
            font-size: 22px;
            margin-bottom: 3px;
        }

        .company-info p {
            font-size: 11px;
            opacity: 0.9;
        }

        .document-title {
            text-align: left;
        }

        .document-title h2 {
            font-size: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 5px;
        }

        .document-title .doc-number {
            font-size: 14px;
            margin-top: 5px;
        }

        /* Content Area */
        .content {
            padding: 20px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .info-card-header {
            background: #34495e;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 14px;
        }

        .info-card-body {
            padding: 10px 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-weight: bold;
            color: #333;
        }

        /* Amount Box */
        .amount-section {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .amount-section .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .amount-section .value {
            font-size: 32px;
            font-weight: bold;
        }

        .amount-details {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .amount-detail {
            text-align: center;
        }

        .amount-detail .label {
            font-size: 11px;
            opacity: 0.8;
        }

        .amount-detail .value {
            font-size: 18px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #cce5ff;
            color: #004085;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Reason Box */
        .reason-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .reason-box h4 {
            color: #495057;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .reason-box p {
            color: #333;
            line-height: 1.6;
        }

        /* Payments Table */
        .payments-section {
            margin: 20px 0;
        }

        .payments-section h3 {
            background: #34495e;
            color: white;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-size: 14px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        .payments-table th,
        .payments-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .payments-table th {
            background: #ecf0f1;
            color: #333;
            font-weight: bold;
        }

        .payments-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .payments-table tfoot {
            background: #34495e;
            color: white;
            font-weight: bold;
        }

        /* Signatures */
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .signature-box {
            text-align: center;
            width: 28%;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 8px;
            font-weight: bold;
            color: #333;
        }

        /* Footer */
        .footer {
            background: #f8f9fa;
            padding: 10px 20px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 2px solid #1a5276;
            margin-top: 20px;
        }

        .footer-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Print Button */
        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .print-btn {
            background: linear-gradient(135deg, #1a5276 0%, #2980b9 100%);
            color: white;
            border: none;
            padding: 12px 40px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .print-btn:hover {
            transform: scale(1.05);
        }

        @media print {
            body {
                padding: 0;
            }

            .container {
                border: 2px solid #333;
            }

            .no-print {
                display: none;
            }

            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .amount-section {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    @if (Auth::user()->CompanyName && Auth::user()->CompanyName->logo)
                        <img src="{{ asset(Auth::user()->CompanyName->logo) }}" alt="Logo">
                    @else
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                    @endif
                </div>
                <div class="company-info">
                    <h1>{{ Auth::user()->CompanyName->name ?? 'شركة الخرسانة الجاهزة' }}</h1>
                    <p>{{ $advance->branch->name ?? '' }} | {{ Auth::user()->CompanyName->phone ?? '' }}</p>
                </div>
            </div>
            <div class="document-title">
                <h2>📄 سند سلفة</h2>
                <div class="doc-number">{{ $advance->advance_number }}</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Info Grid -->
            <div class="info-grid">
                <!-- Beneficiary Info -->
                <div class="info-card">
                    <div class="info-card-header">👤 معلومات المستفيد</div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">الاسم:</span>
                            <span class="info-value">{{ $advance->beneficiary_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">النوع:</span>
                            <span class="info-value">
                                @switch($advance->beneficiary_type)
                                    @case('employee')
                                        موظف
                                    @break

                                    @case('agent')
                                        مندوب
                                    @break

                                    @case('supplier')
                                        مورد
                                    @break

                                    @case('contractor')
                                        مقاول
                                    @break
                                @endswitch
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">الفرع:</span>
                            <span class="info-value">{{ $advance->branch->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Advance Info -->
                <div class="info-card">
                    <div class="info-card-header">📋 تفاصيل السلفة</div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">تاريخ الطلب:</span>
                            <span class="info-value">{{ $advance->requested_at?->format('Y-m-d') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">الحالة:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $advance->status }}">
                                    @switch($advance->status)
                                        @case('pending')
                                            معلقة
                                        @break

                                        @case('approved')
                                            موافق عليها
                                        @break

                                        @case('active')
                                            نشطة
                                        @break

                                        @case('completed')
                                            مكتملة
                                        @break

                                        @case('cancelled')
                                            ملغاة
                                        @break
                                    @endswitch
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">الاستقطاع:</span>
                            <span class="info-value">
                                {{ number_format($advance->deduction_value) }}{{ $advance->deduction_type == 'percentage' ? '%' : ' د.ع' }}
                                ({{ $advance->deduction_type == 'percentage' ? 'نسبة' : 'ثابت' }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Section -->
            <div class="amount-section">
                <div class="label">مبلغ السلفة</div>
                <div class="value">{{ number_format($advance->amount) }} د.ع</div>
                <div class="amount-details">
                    <div class="amount-detail">
                        <div class="label">المسدد</div>
                        <div class="value">{{ number_format($advance->paid_amount) }} د.ع</div>
                    </div>
                    <div class="amount-detail">
                        <div class="label">المتبقي</div>
                        <div class="value">{{ number_format($advance->remaining_amount) }} د.ع</div>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            @if ($advance->reason)
                <div class="reason-box">
                    <h4>📝 سبب السلفة:</h4>
                    <p>{{ $advance->reason }}</p>
                </div>
            @endif

            <!-- Payments -->
            @if ($advance->payments->count() > 0)
                <div class="payments-section">
                    <h3>💳 سجل الدفعات</h3>
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الدفعة</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                                <th>النوع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advance->payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $payment->payment_number }}</td>
                                    <td>{{ number_format($payment->amount) }} د.ع</td>
                                    <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                    <td>
                                        @switch($payment->payment_type)
                                            @case('manual')
                                                يدوي
                                            @break

                                            @case('salary_deduction')
                                                خصم راتب
                                            @break

                                            @case('invoice_deduction')
                                                خصم فاتورة
                                            @break

                                            @case('commission_deduction')
                                                خصم عمولة
                                            @break
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">الإجمالي</td>
                                <td>{{ number_format($advance->payments->sum('amount')) }} د.ع</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">المستفيد</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">المحاسب</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">المدير</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <span>تم الطباعة: {{ now()->format('Y-m-d H:i') }}</span>
                <span>{{ $advance->branch->company->name ?? 'شركة الخرسانة' }} - جميع الحقوق محفوظة</span>
                <span>صفحة 1 من 1</span>
            </div>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="print-btn">
            🖨️ طباعة السند
        </button>
    </div>
</body>

</html>
