<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>ملخص الصندوق - {{ $summary->summary_date->format('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            padding: 20px;
        }

        .report {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
        }

        .report-title {
            font-size: 14pt;
            margin: 10px 0;
            background: #007bff;
            color: white;
            padding: 5px 20px;
            display: inline-block;
            border-radius: 5px;
        }

        .report-date {
            font-size: 12pt;
            margin-top: 10px;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 14pt;
            background: #e9ecef;
            margin: 10px -15px -15px;
            padding: 15px;
            border-radius: 0 0 10px 10px;
        }

        .label {
            color: #666;
        }

        .value {
            font-weight: bold;
        }

        .value.positive {
            color: #28a745;
        }

        .value.negative {
            color: #dc3545;
        }

        .section-title {
            background: #333;
            color: white;
            padding: 8px 15px;
            margin: 20px 0 10px;
            font-size: 12pt;
        }

        .section-title.receipts {
            background: #28a745;
        }

        .section-title.payments {
            background: #dc3545;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        .transactions-table th {
            background: #f5f5f5;
        }

        .transactions-table .text-end {
            text-align: left;
        }

        .transactions-table tfoot {
            font-weight: bold;
            background: #f0f0f0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9pt;
        }

        .status-open {
            background: #28a745;
            color: white;
        }

        .status-closed {
            background: #6c757d;
            color: white;
        }

        .closing-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }

        .signature {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10pt;
        }

        .timestamp {
            text-align: center;
            font-size: 9pt;
            color: #999;
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
            style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
            🖨️ طباعة
        </button>
    </div>

    <div class="report">
        <!-- الرأس -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <div class="report-title">تقرير الصندوق اليومي</div>
            <div class="report-date">
                {{ $summary->summary_date->locale('ar')->format('l j F Y') }}
            </div>
            <div style="margin-top: 10px;">
                <span class="status-badge status-{{ $summary->is_open ? 'open' : 'closed' }}">
                    {{ $summary->status_label }}
                </span>
            </div>
        </div>

        <!-- ملخص الصندوق -->
        <div class="summary-section">
            <div class="summary-row">
                <span class="label">الرصيد الافتتاحي:</span>
                <span class="value">{{ number_format($summary->opening_balance, 0) }} د.ع</span>
            </div>
            <div class="summary-row">
                <span class="label">إجمالي المقبوضات ({{ $summary->receipts_count }}):</span>
                <span class="value positive">+ {{ number_format($summary->total_receipts, 0) }} د.ع</span>
            </div>
            <div class="summary-row">
                <span class="label">إجمالي المدفوعات ({{ $summary->payments_count }}):</span>
                <span class="value negative">- {{ number_format($summary->total_payments, 0) }} د.ع</span>
            </div>
            <div class="summary-row total">
                <span>الرصيد الختامي:</span>
                <span>{{ number_format($summary->closing_balance, 0) }} د.ع</span>
            </div>
        </div>

        <!-- المقبوضات -->
        @if (count($details['receipts']) > 0)
            <div class="section-title receipts">المقبوضات</div>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الوقت</th>
                        <th>الدافع</th>
                        <th>البيان</th>
                        <th class="text-end">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details['receipts'] as $receipt)
                        <tr>
                            <td>{{ $receipt->receipt_number }}</td>
                            <td>{{ $receipt->received_at->format('H:i') }}</td>
                            <td>{{ $receipt->payer_name }}</td>
                            <td>{{ Str::limit($receipt->description, 40) }}</td>
                            <td class="text-end">{{ number_format($receipt->amount_in_default, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">الإجمالي</td>
                        <td class="text-end">{{ number_format($details['total_receipts'], 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- المدفوعات -->
        @if (count($details['vouchers']) > 0)
            <div class="section-title payments">المدفوعات</div>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الوقت</th>
                        <th>المستفيد</th>
                        <th>البيان</th>
                        <th class="text-end">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details['vouchers'] as $voucher)
                        <tr>
                            <td>{{ $voucher->voucher_number }}</td>
                            <td>{{ $voucher->paid_at->format('H:i') }}</td>
                            <td>{{ $voucher->payee_name }}</td>
                            <td>{{ Str::limit($voucher->description, 40) }}</td>
                            <td class="text-end">{{ number_format($voucher->amount_in_default, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">الإجمالي</td>
                        <td class="text-end">{{ number_format($details['total_vouchers'], 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- معلومات الإقفال -->
        @if ($summary->closed_at)
            <div class="closing-info">
                <strong>تم الإقفال:</strong> {{ $summary->closed_at->format('Y-m-d H:i') }}
                @if ($summary->closedByUser)
                    بواسطة: {{ $summary->closedByUser->name }}
                @endif
                @if ($summary->notes)
                    <br><strong>ملاحظات:</strong> {{ $summary->notes }}
                @endif
            </div>
        @endif

        <!-- التوقيعات -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line">أمين الصندوق</div>
            </div>
            <div class="signature">
                <div class="signature-line">المحاسب</div>
            </div>
            <div class="signature">
                <div class="signature-line">المدير</div>
            </div>
        </div>

        <div class="timestamp">
            تم الطباعة: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>

</html>
