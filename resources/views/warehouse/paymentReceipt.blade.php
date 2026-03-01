<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفع - {{ $payment->payment_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
            direction: rtl;
        }

        .receipt-container {
            max-width: 450px;
            margin: 0 auto;
        }

        .receipt {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Header with Logo */
        .receipt-header {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 15px solid #2d3748;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .company-logo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            color: white;
        }

        .company-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 12px;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Receipt Title */
        .receipt-title {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            padding: 20px;
            text-align: center;
            margin-top: 15px;
        }

        .receipt-title h2 {
            color: white;
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .receipt-title h2 svg {
            width: 28px;
            height: 28px;
        }

        .receipt-number {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: white;
            letter-spacing: 1px;
        }

        /* Receipt Body */
        .receipt-body {
            padding: 25px 20px;
        }

        .info-section {
            background: #f7fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #718096;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label svg {
            width: 16px;
            height: 16px;
            opacity: 0.7;
        }

        .info-value {
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }

        /* Amount Section */
        .amount-section {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .amount-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
        }

        .amount-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 8px;
            position: relative;
        }

        .amount-value {
            font-size: 36px;
            font-weight: 700;
            color: white;
            position: relative;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .amount-currency {
            font-size: 16px;
            margin-right: 5px;
            opacity: 0.9;
        }

        /* Balance Section */
        .balance-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .balance-box {
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .balance-before {
            background: linear-gradient(135deg, #fbd38d 0%, #f6ad55 100%);
        }

        .balance-after {
            background: linear-gradient(135deg, #90cdf4 0%, #63b3ed 100%);
        }

        .balance-label {
            font-size: 11px;
            color: rgba(0, 0, 0, 0.7);
            margin-bottom: 5px;
        }

        .balance-value {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }

        /* Payment Method Badge */
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-cash {
            background: #c6f6d5;
            color: #276749;
        }

        .badge-transfer {
            background: #bee3f8;
            color: #2b6cb0;
        }

        .badge-check {
            background: #feebc8;
            color: #c05621;
        }

        /* Notes Section */
        .notes-section {
            background: #fffaf0;
            border: 1px solid #fbd38d;
            border-radius: 10px;
            padding: 12px 15px;
            margin-top: 15px;
            font-size: 13px;
            color: #744210;
        }

        .notes-section strong {
            display: block;
            margin-bottom: 5px;
            color: #975a16;
        }

        /* Signature Section */
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #e2e8f0;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #2d3748;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 12px;
            color: #718096;
        }

        /* Footer */
        .receipt-footer {
            background: #f7fafc;
            padding: 20px;
            text-align: center;
            border-top: 2px dashed #e2e8f0;
        }

        .footer-info {
            font-size: 11px;
            color: #718096;
            margin: 4px 0;
        }

        .footer-thanks {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }

        .footer-thanks span {
            color: #e53e3e;
        }

        /* Print Button */
        .print-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            max-width: 450px;
            margin: 25px auto;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .print-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.03);
            pointer-events: none;
            white-space: nowrap;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-btn {
                display: none !important;
            }

            .receipt-container {
                max-width: 100%;
            }

            .receipt {
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt">
            {{-- Header with Logo --}}
            <div class="receipt-header">
                <div class="company-logo">
                    @if ($company->logo ?? false)
                        <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}">
                    @else
                        <div class="company-logo-placeholder">
                            {{ mb_substr($company->name ?? 'ش', 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="company-name">{{ $company->name ?? 'الشركة' }}</div>
                <div class="company-info">
                    @if ($company->address ?? false)
                        {{ $company->address }}<br>
                    @endif
                    @if ($company->phone ?? false)
                        📞 {{ $company->phone }}
                    @endif
                </div>
            </div>

            {{-- Receipt Title --}}
            <div class="receipt-title">
                <h2>
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                    </svg>
                    إيصال تسديد
                </h2>
                <div class="receipt-number">{{ $payment->payment_number }}</div>
            </div>

            {{-- Receipt Body --}}
            <div class="receipt-body" style="position: relative;">
                <div class="watermark">مدفوع</div>

                {{-- Info Section --}}
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" />
                            </svg>
                            التاريخ
                        </span>
                        <span class="info-value">{{ $payment->created_at->format('Y/m/d - h:i A') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                            </svg>
                            المورد
                        </span>
                        <span class="info-value">{{ $payment->supplier->supplier_name }}</span>
                    </div>

                    @if ($payment->supplier->company_name)
                        <div class="info-row">
                            <span class="info-label">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                                </svg>
                                الشركة
                            </span>
                            <span class="info-value">{{ $payment->supplier->company_name }}</span>
                        </div>
                    @endif

                    <div class="info-row">
                        <span class="info-label">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                            الفرع
                        </span>
                        <span class="info-value">{{ $payment->supplier->branchName->branch_name ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                            </svg>
                            طريقة الدفع
                        </span>
                        <span class="info-value">
                            @if ($payment->payment_method == 'cash')
                                <span class="payment-method-badge badge-cash">💵 نقدي</span>
                            @elseif($payment->payment_method == 'bank_transfer')
                                <span class="payment-method-badge badge-transfer">🏦 تحويل بنكي</span>
                            @else
                                <span class="payment-method-badge badge-check">📝 شيك</span>
                            @endif
                        </span>
                    </div>

                    @if ($payment->reference_number)
                        <div class="info-row">
                            <span class="info-label">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-1 12H5c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v8c0 .55-.45 1-1 1z" />
                                </svg>
                                رقم المرجع
                            </span>
                            <span class="info-value"
                                style="font-family: monospace; letter-spacing: 1px;">{{ $payment->reference_number }}</span>
                        </div>
                    @endif
                </div>

                {{-- Amount Section --}}
                <div class="amount-section">
                    <div class="amount-label">المبلغ المدفوع</div>
                    <div class="amount-value">
                        {{ number_format($payment->amount, 0) }}
                        <span class="amount-currency">د.ع</span>
                    </div>
                </div>

                {{-- Balance Section --}}
                <div class="balance-section">
                    <div class="balance-box balance-before">
                        <div class="balance-label">الرصيد قبل الدفع</div>
                        <div class="balance-value">{{ number_format($payment->balance_before, 0) }}</div>
                    </div>
                    <div class="balance-box balance-after">
                        <div class="balance-label">الرصيد بعد الدفع</div>
                        <div class="balance-value">{{ number_format($payment->balance_after, 0) }}</div>
                    </div>
                </div>

                {{-- Notes --}}
                @if ($payment->notes)
                    <div class="notes-section">
                        <strong>📝 ملاحظات:</strong>
                        {{ $payment->notes }}
                    </div>
                @endif

                {{-- Signature Section --}}
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">توقيع المستلم</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">توقيع المحاسب</div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="receipt-footer">
                <div class="footer-info">
                    <strong>تم التسجيل بواسطة:</strong> {{ $payment->createdBy->fullname ?? '-' }}
                </div>
                <div class="footer-info">
                    <strong>تاريخ الطباعة:</strong> {{ now()->format('Y/m/d h:i A') }}
                </div>
                <div class="footer-thanks">
                    شكراً لتعاملكم معنا <span>❤</span>
                </div>
            </div>
        </div>

        {{-- Print Button --}}
        <button class="print-btn" onclick="window.print()">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z" />
            </svg>
            طباعة الإيصال
        </button>
    </div>
</body>

</html>
