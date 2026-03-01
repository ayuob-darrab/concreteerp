<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>كشف حساب - {{ $balance->account_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            padding: 20px;
        }

        .statement {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
        }

        .statement-title {
            font-size: 14pt;
            margin: 10px 0;
            background: #333;
            color: white;
            padding: 5px 20px;
            display: inline-block;
        }

        .account-info {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .account-info table {
            width: 100%;
        }

        .account-info th {
            text-align: right;
            width: 100px;
            color: #666;
            font-weight: normal;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .summary-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 24%;
            border-radius: 5px;
        }

        .summary-box .label {
            font-size: 9pt;
            color: #666;
        }

        .summary-box .value {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        .transactions-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .transactions-table .text-center {
            text-align: center;
        }

        .transactions-table .text-left {
            text-align: left;
        }

        .debit {
            color: #28a745;
        }

        .credit {
            color: #dc3545;
        }

        .balance-positive {
            color: #28a745;
        }

        .balance-negative {
            color: #dc3545;
        }

        .opening-row,
        .closing-row {
            background: #e9ecef;
            font-weight: bold;
        }

        .closing-row {
            background: #cfe2ff;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
        }

        .signature {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
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
            style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #333; color: white; border: none; border-radius: 5px;">
            🖨️ طباعة
        </button>
    </div>

    <div class="statement">
        <!-- الرأس -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <div class="statement-title">كشف حساب</div>
            <div style="margin-top: 10px; font-size: 9pt; color: #666;">
                الفترة من {{ $fromDate }} إلى {{ $toDate }}
            </div>
        </div>

        <!-- معلومات الحساب -->
        <div class="account-info">
            <table>
                <tr>
                    <th>صاحب الحساب:</th>
                    <td><strong>{{ $balance->account_name }}</strong></td>
                    <th>نوع الحساب:</th>
                    <td>{{ $balance->account_type_label }}</td>
                </tr>
                @if ($balance->account_phone)
                    <tr>
                        <th>الهاتف:</th>
                        <td colspan="3">{{ $balance->account_phone }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- ملخص -->
        <div class="summary-row">
            <div class="summary-box">
                <div class="label">رصيد أول المدة</div>
                <div class="value">{{ number_format($openingBalance, 0) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">إجمالي المدين</div>
                <div class="value debit">{{ number_format($periodDebits, 0) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">إجمالي الدائن</div>
                <div class="value credit">{{ number_format($periodCredits, 0) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">رصيد آخر المدة</div>
                <div class="value {{ $closingBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    {{ number_format(abs($closingBalance), 0) }}
                </div>
            </div>
        </div>

        <!-- جدول الحركات -->
        <table class="transactions-table">
            <thead>
                <tr>
                    <th style="width: 80px;">التاريخ</th>
                    <th style="width: 100px;">المستند</th>
                    <th>البيان</th>
                    <th style="width: 90px;" class="text-center">مدين</th>
                    <th style="width: 90px;" class="text-center">دائن</th>
                    <th style="width: 100px;" class="text-center">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                <tr class="opening-row">
                    <td>{{ $fromDate }}</td>
                    <td>-</td>
                    <td>رصيد أول المدة</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">{{ number_format($openingBalance, 0) }}</td>
                </tr>
                @php $runningBalance = $openingBalance; @endphp
                @foreach ($transactions as $transaction)
                    @php
                        $runningBalance += $transaction->debit - $transaction->credit;
                    @endphp
                    <tr>
                        <td>{{ $transaction->date->format('Y-m-d') }}</td>
                        <td>
                            {{ $transaction->document_type === 'receipt' ? 'قبض' : ($transaction->document_type === 'voucher' ? 'صرف' : '') }}
                            {{ $transaction->document_number }}
                        </td>
                        <td>{{ $transaction->description }}</td>
                        <td class="text-center debit">
                            {{ $transaction->debit > 0 ? number_format($transaction->debit, 0) : '-' }}
                        </td>
                        <td class="text-center credit">
                            {{ $transaction->credit > 0 ? number_format($transaction->credit, 0) : '-' }}
                        </td>
                        <td class="text-center {{ $runningBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format(abs($runningBalance), 0) }}
                        </td>
                    </tr>
                @endforeach
                <tr class="closing-row">
                    <td>{{ $toDate }}</td>
                    <td>-</td>
                    <td>رصيد آخر المدة</td>
                    <td class="text-center debit">{{ number_format($periodDebits, 0) }}</td>
                    <td class="text-center credit">{{ number_format($periodCredits, 0) }}</td>
                    <td class="text-center {{ $closingBalance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                        {{ number_format(abs($closingBalance), 0) }}
                        {{ $closingBalance >= 0 ? '(له)' : '(عليه)' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- التوقيعات -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line">المحاسب</div>
            </div>
            <div class="signature">
                <div class="signature-line">المدير المالي</div>
            </div>
            <div class="signature">
                <div class="signature-line">صاحب الحساب</div>
            </div>
        </div>

        <!-- التذييل -->
        <div class="footer">
            تم الطباعة: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>

</html>
