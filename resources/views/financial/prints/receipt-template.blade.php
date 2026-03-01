<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إيصال قبض #{{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20px;
        }

        .receipt {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #28a745;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #28a745;
        }

        .branch-name {
            font-size: 11pt;
            color: #666;
        }

        .receipt-title {
            font-size: 18pt;
            margin: 10px 0;
            background: #28a745;
            color: white;
            padding: 5px 20px;
            display: inline-block;
            border-radius: 5px;
        }

        .receipt-number {
            font-size: 14pt;
            color: #333;
            margin: 5px 0;
        }

        .receipt-date {
            font-size: 11pt;
            color: #666;
        }

        .content {
            padding: 15px 0;
        }

        .field {
            margin-bottom: 12px;
        }

        .field-label {
            color: #666;
            font-size: 10pt;
            display: block;
            margin-bottom: 2px;
        }

        .field-value {
            font-size: 13pt;
            font-weight: bold;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 3px;
        }

        .amount-box {
            background: #e8f5e9;
            border: 2px solid #28a745;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin: 15px 0;
        }

        .amount-value {
            font-size: 28pt;
            font-weight: bold;
            color: #28a745;
        }

        .amount-words {
            font-size: 11pt;
            color: #333;
            margin-top: 5px;
        }

        .payment-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .footer {
            border-top: 2px dashed #28a745;
            padding-top: 15px;
            margin-top: 15px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }

        .timestamp {
            font-size: 9pt;
            color: #999;
            text-align: center;
            margin-top: 20px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 5px;">
            🖨️ طباعة
        </button>
    </div>

    <div class="receipt">
        <!-- الرأس -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <div class="branch-name">{{ $receipt->branch->name ?? '' }}</div>
            <div class="receipt-title">إيصال قبض</div>
            <div class="receipt-number">رقم: {{ $receipt->receipt_number }}</div>
            <div class="receipt-date">التاريخ: {{ $receipt->received_at->format('Y/m/d') }} -
                {{ $receipt->received_at->format('H:i') }}</div>
        </div>

        <!-- المحتوى -->
        <div class="content">
            <div class="field">
                <span class="field-label">استلمنا من السيد/ة:</span>
                <span class="field-value">{{ $receipt->payer_name }}</span>
            </div>

            @if ($receipt->payer_phone)
                <div class="field">
                    <span class="field-label">رقم الهاتف:</span>
                    <span class="field-value">{{ $receipt->payer_phone }}</span>
                </div>
            @endif

            <div class="amount-box">
                <div class="amount-value">{{ number_format($receipt->amount, 0) }}
                    {{ $receipt->currency->symbol ?? 'د.ع' }}</div>
                @if ($receipt->amount_in_words)
                    <div class="amount-words">{{ $receipt->amount_in_words }}</div>
                @endif
            </div>

            <div class="field">
                <span class="field-label">وذلك عن:</span>
                <span class="field-value">{{ $receipt->description }}</span>
            </div>

            <div class="payment-info">
                <strong>طريقة الدفع:</strong> {{ $receipt->payment_method_label }}
                @if ($receipt->reference_number)
                    <br><strong>رقم المرجع:</strong> {{ $receipt->reference_number }}
                @endif
                @if ($receipt->check_number)
                    <br><strong>رقم الشيك:</strong> {{ $receipt->check_number }}
                    @if ($receipt->check_date)
                        - تاريخ: {{ $receipt->check_date->format('Y/m/d') }}
                    @endif
                @endif
                @if ($receipt->bank_name)
                    <br><strong>البنك:</strong> {{ $receipt->bank_name }}
                @endif
            </div>
        </div>

        <!-- التذييل -->
        <div class="footer">
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line">المستلم</div>
                    <small>{{ $receipt->receiver->name ?? '' }}</small>
                </div>
                <div class="signature">
                    <div class="signature-line">الدافع</div>
                </div>
            </div>
        </div>

        <div class="timestamp">
            تم الإنشاء: {{ $receipt->created_at->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>

</html>
