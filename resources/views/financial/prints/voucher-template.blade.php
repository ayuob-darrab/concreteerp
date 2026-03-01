<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>سند صرف #{{ $voucher->voucher_number }}</title>
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

        .voucher {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #dc3545;
            padding: 20px;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #dc3545;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #dc3545;
        }

        .branch-name {
            font-size: 11pt;
            color: #666;
        }

        .voucher-title {
            font-size: 18pt;
            margin: 10px 0;
            background: #dc3545;
            color: white;
            padding: 5px 20px;
            display: inline-block;
            border-radius: 5px;
        }

        .voucher-number {
            font-size: 14pt;
            color: #333;
            margin: 5px 0;
        }

        .voucher-date {
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
            background: #ffebee;
            border: 2px solid #dc3545;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin: 15px 0;
        }

        .amount-value {
            font-size: 28pt;
            font-weight: bold;
            color: #dc3545;
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

        .approval-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .footer {
            border-top: 2px dashed #dc3545;
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
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10pt;
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
            style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #dc3545; color: white; border: none; border-radius: 5px;">
            🖨️ طباعة
        </button>
    </div>

    <div class="voucher">
        <!-- الرأس -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <div class="branch-name">{{ $voucher->branch->name ?? '' }}</div>
            <div class="voucher-title">سند صرف</div>
            <div class="voucher-number">رقم: {{ $voucher->voucher_number }}</div>
            <div class="voucher-date">التاريخ:
                {{ $voucher->paid_at ? $voucher->paid_at->format('Y/m/d') : $voucher->created_at->format('Y/m/d') }}
            </div>
        </div>

        <!-- المحتوى -->
        <div class="content">
            <div class="field">
                <span class="field-label">صُرف لـ:</span>
                <span class="field-value">{{ $voucher->payee_name }}</span>
            </div>

            <div class="field">
                <span class="field-label">الصفة:</span>
                <span class="field-value">{{ $voucher->payee_type_label }}</span>
            </div>

            <div class="amount-box">
                <div class="amount-value">{{ number_format($voucher->amount, 0) }}
                    {{ $voucher->currency->symbol ?? 'د.ع' }}</div>
                @if ($voucher->amount_in_words)
                    <div class="amount-words">{{ $voucher->amount_in_words }}</div>
                @endif
            </div>

            <div class="field">
                <span class="field-label">وذلك عن:</span>
                <span class="field-value">{{ $voucher->description }}</span>
            </div>

            <div class="payment-info">
                <strong>طريقة الدفع:</strong> {{ $voucher->payment_method_label }}
                @if ($voucher->reference_number)
                    <br><strong>رقم المرجع:</strong> {{ $voucher->reference_number }}
                @endif
                @if ($voucher->check_number)
                    <br><strong>رقم الشيك:</strong> {{ $voucher->check_number }}
                    @if ($voucher->check_date)
                        - تاريخ: {{ $voucher->check_date->format('Y/m/d') }}
                    @endif
                @endif
                @if ($voucher->bank_name)
                    <br><strong>البنك:</strong> {{ $voucher->bank_name }}
                @endif
            </div>

            @if ($voucher->approver)
                <div class="approval-box">
                    <strong>الموافقة:</strong> {{ $voucher->approver->name }}
                    <br><small>{{ $voucher->approved_at->format('Y-m-d H:i') }}</small>
                </div>
            @endif
        </div>

        <!-- التذييل -->
        <div class="footer">
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line">أعدّه</div>
                    <small>{{ $voucher->creator->name ?? '' }}</small>
                </div>
                <div class="signature">
                    <div class="signature-line">المحاسب</div>
                    <small>{{ $voucher->payer->name ?? '' }}</small>
                </div>
                <div class="signature">
                    <div class="signature-line">المستلم</div>
                </div>
            </div>
        </div>

        <div class="timestamp">
            تم الصرف: {{ $voucher->paid_at ? $voucher->paid_at->format('Y-m-d H:i:s') : 'غير مصروف' }}
        </div>
    </div>
</body>

</html>
