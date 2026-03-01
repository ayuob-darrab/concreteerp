@extends('layouts.app')

@section('title', 'طباعة السند')

@section('content')
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $receipt->type_label }} رقم {{ $receipt->receipt_number }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
                font-size: 14px;
                line-height: 1.6;
                color: #333;
                background: #fff;
                direction: rtl;
            }

            .receipt-container {
                max-width: 600px;
                margin: 20px auto;
                padding: 30px;
                border: 2px solid #2c3e50;
                border-radius: 10px;
            }

            .receipt-header {
                text-align: center;
                padding-bottom: 20px;
                border-bottom: 2px dashed #ccc;
                margin-bottom: 20px;
            }

            .company-name {
                font-size: 22px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .receipt-type {
                font-size: 28px;
                font-weight: bold;
                color: {{ $receipt->receipt_type === 'receipt' ? '#27ae60' : '#e74c3c' }};
                margin: 15px 0;
                padding: 10px;
                background: {{ $receipt->receipt_type === 'receipt' ? '#e8f8f5' : '#fef5f5' }};
                border-radius: 5px;
            }

            .receipt-number {
                font-size: 16px;
                color: #666;
            }

            .receipt-body {
                margin: 20px 0;
            }

            .info-row {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-bottom: 1px solid #eee;
            }

            .info-row:nth-child(odd) {
                background: #f9f9f9;
            }

            .info-label {
                font-weight: bold;
                color: #666;
            }

            .info-value {
                color: #333;
            }

            .amount-section {
                text-align: center;
                margin: 30px 0;
                padding: 20px;
                background: {{ $receipt->receipt_type === 'receipt' ? '#27ae60' : '#e74c3c' }};
                color: #fff;
                border-radius: 10px;
            }

            .amount-label {
                font-size: 14px;
                opacity: 0.9;
            }

            .amount-value {
                font-size: 32px;
                font-weight: bold;
                margin: 10px 0;
            }

            .amount-words {
                font-size: 14px;
                opacity: 0.9;
            }

            .signatures {
                display: flex;
                justify-content: space-between;
                margin-top: 50px;
                padding-top: 20px;
            }

            .signature-box {
                text-align: center;
                width: 45%;
            }

            .signature-line {
                border-top: 1px solid #333;
                margin-top: 40px;
                padding-top: 5px;
            }

            .receipt-footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px dashed #ccc;
                font-size: 12px;
                color: #666;
            }

            .status-badge {
                display: inline-block;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
            }

            .status-approved {
                background: #27ae60;
                color: #fff;
            }

            .status-draft {
                background: #f39c12;
                color: #fff;
            }

            .status-cancelled {
                background: #e74c3c;
                color: #fff;
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .receipt-container {
                    border: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="receipt-container">
            {{-- رأس السند --}}
            <div class="receipt-header">
                <div class="company-name">{{ $company->name ?? 'اسم الشركة' }}</div>
                <p>{{ $company->address ?? '' }}</p>
                <div class="receipt-type">{{ $receipt->type_label }}</div>
                <div class="receipt-number">رقم: {{ $receipt->receipt_number }}</div>
            </div>

            {{-- جسم السند --}}
            <div class="receipt-body">
                <div class="info-row">
                    <span class="info-label">التاريخ:</span>
                    <span class="info-value">{{ $receipt->receipt_date->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span
                        class="info-label">{{ $receipt->receipt_type === 'receipt' ? 'استلمنا من' : 'صرفنا إلى' }}:</span>
                    <span class="info-value">{{ $receipt->party_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">طريقة الدفع:</span>
                    <span class="info-value">{{ $receipt->payment_method_label }}</span>
                </div>
                @if ($receipt->payment_reference)
                    <div class="info-row">
                        <span class="info-label">رقم المرجع:</span>
                        <span class="info-value">{{ $receipt->payment_reference }}</span>
                    </div>
                @endif
                @if ($receipt->bank_name)
                    <div class="info-row">
                        <span class="info-label">البنك:</span>
                        <span class="info-value">{{ $receipt->bank_name }}</span>
                    </div>
                @endif
                @if ($receipt->invoice)
                    <div class="info-row">
                        <span class="info-label">للفاتورة رقم:</span>
                        <span class="info-value">{{ $receipt->invoice->invoice_number }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">الحالة:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $receipt->status }}">{{ $receipt->status_label }}</span>
                    </span>
                </div>
            </div>

            {{-- المبلغ --}}
            <div class="amount-section">
                <div class="amount-label">المبلغ</div>
                <div class="amount-value">{{ number_format($receipt->amount, 2) }} د.ع</div>
                <div class="amount-words">
                    {{ $receipt->description ?? 'فقط ' . \App\Helpers\NumberToWords::convert($receipt->amount) . ' دينار عراقي لا غير' }}
                </div>
            </div>

            {{-- ملاحظات --}}
            @if ($receipt->notes)
                <div class="info-row">
                    <span class="info-label">ملاحظات:</span>
                    <span class="info-value">{{ $receipt->notes }}</span>
                </div>
            @endif

            {{-- التوقيعات --}}
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">المستلم</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">أمين الصندوق</div>
                </div>
            </div>

            {{-- التذييل --}}
            <div class="receipt-footer">
                <p>هذا السند صادر إلكترونياً</p>
                <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
                @if ($receipt->creator)
                    <p>بواسطة: {{ $receipt->creator->name }}</p>
                @endif
            </div>
        </div>

        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>

    </html>
@endsection
