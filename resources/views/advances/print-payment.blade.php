<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفعة سلفة - {{ $payment->payment_number }}</title>
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
            max-width: 700px;
            margin: 0 auto;
            border: 3px solid #27ae60;
            padding: 0;
        }

        /* Header with Logo */
        .header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
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
            width: 60px;
            height: 60px;
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
            font-size: 20px;
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
            font-size: 18px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 5px;
        }

        .document-title .doc-number {
            font-size: 13px;
            margin-top: 5px;
        }

        /* Content Area */
        .content {
            padding: 20px;
        }

        /* Receipt Info */
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #27ae60;
        }

        .receipt-header h3 {
            color: #27ae60;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .receipt-header .date {
            color: #666;
            font-size: 14px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
            padding: 8px 12px;
            font-weight: bold;
            font-size: 13px;
        }

        .info-card-body {
            padding: 10px 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
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
        .amount-box {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .amount-box .label {
            font-size: 14px;
            margin-bottom: 5px;
            opacity: 0.9;
        }

        .amount-box .amount {
            font-size: 32px;
            font-weight: bold;
        }

        .amount-box .amount-text {
            font-size: 13px;
            margin-top: 5px;
            opacity: 0.9;
        }

        /* Balance Info */
        .balance-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .balance-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .balance-card.before {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .balance-card.after {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .balance-card .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .balance-card .value {
            font-size: 20px;
            font-weight: bold;
        }

        .balance-card.before .value {
            color: #6c757d;
        }

        .balance-card.after .value {
            color: #28a745;
        }

        /* Notes */
        .notes-section {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .notes-section h4 {
            color: #495057;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .notes-section p {
            color: #666;
            font-size: 12px;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            color: #666;
            font-size: 12px;
            margin-bottom: 30px;
        }

        .signature-box .line {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 12px;
            color: #333;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-btn:hover {
            background: #219a52;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                padding: 0;
            }

            .container {
                border: 2px solid #27ae60;
            }
        }
    </style>
</head>

<body>
    <button class="print-btn" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        طباعة الإيصال
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    @if (Auth::user()->CompanyName && Auth::user()->CompanyName->logo)
                        <img src="{{ asset(Auth::user()->CompanyName->logo) }}" alt="Logo">
                    @else
                        <span style="font-size: 30px;">🏢</span>
                    @endif
                </div>
                <div class="company-info">
                    <h1>{{ Auth::user()->CompanyName->name ?? 'الشركة' }}</h1>
                    <p>{{ $payment->advance->branch->name ?? '' }}</p>
                </div>
            </div>
            <div class="document-title">
                <h2>إيصال تسديد سلفة</h2>
                <div class="doc-number">{{ $payment->payment_number }}</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Receipt Header -->
            <div class="receipt-header">
                <h3>✓ إيصال استلام دفعة</h3>
                <div class="date">{{ $payment->paid_at?->format('Y-m-d') ?? now()->format('Y-m-d') }}</div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <!-- معلومات السلفة -->
                <div class="info-card">
                    <div class="info-card-header">📋 معلومات السلفة</div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">رقم السلفة:</span>
                            <span class="info-value">{{ $payment->advance->advance_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">المستفيد:</span>
                            <span class="info-value">{{ $payment->advance->beneficiary_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">مبلغ السلفة الكلي:</span>
                            <span class="info-value">{{ number_format($payment->advance->amount) }} د.ع</span>
                        </div>
                    </div>
                </div>

                <!-- معلومات الدفعة -->
                <div class="info-card">
                    <div class="info-card-header">💳 معلومات الدفعة</div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">رقم الدفعة:</span>
                            <span class="info-value">{{ $payment->payment_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">نوع الدفعة:</span>
                            <span class="info-value">
                                @switch($payment->payment_type)
                                    @case('manual')
                                        دفع يدوي
                                    @break

                                    @case('salary_deduction')
                                        خصم من الراتب
                                    @break

                                    @case('invoice_deduction')
                                        خصم من فاتورة
                                    @break

                                    @default
                                        {{ $payment->payment_type }}
                                @endswitch
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">تاريخ الدفع:</span>
                            <span class="info-value">{{ $payment->paid_at?->format('Y-m-d') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Box -->
            <div class="amount-box">
                <div class="label">مبلغ الدفعة</div>
                <div class="amount">{{ number_format($payment->amount) }} د.ع</div>
            </div>

            <!-- Balance Info -->
            <div class="balance-info">
                <div class="balance-card before">
                    <div class="label">الرصيد قبل الدفعة</div>
                    <div class="value">{{ number_format($payment->balance_before) }} د.ع</div>
                </div>
                <div class="balance-card after">
                    <div class="label">الرصيد بعد الدفعة</div>
                    <div class="value">{{ number_format($payment->balance_after) }} د.ع</div>
                </div>
            </div>

            @if ($payment->notes)
                <div class="notes-section">
                    <h4>📝 ملاحظات:</h4>
                    <p>{{ $payment->notes }}</p>
                </div>
            @endif

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="title">توقيع المستلم</div>
                    <div class="line">{{ $payment->advance->beneficiary_name }}</div>
                </div>
                <div class="signature-box">
                    <div class="title">توقيع أمين الصندوق</div>
                    <div class="line">{{ $payment->payer->name ?? Auth::user()->name }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            تم الطباعة بتاريخ: {{ now()->format('Y-m-d H:i') }} |
            {{ Auth::user()->CompanyName->name ?? 'ConcreteERP' }}
        </div>
    </div>
</body>

</html>
