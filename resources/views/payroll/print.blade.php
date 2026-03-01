<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف راتب - {{ $payroll->employee->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            direction: rtl;
            padding: 20px;
            font-size: 13px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
        }

        .header {
            background: #1a5276;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: normal;
        }

        .info-row {
            display: flex;
            border-bottom: 1px solid #ddd;
        }

        .info-row>div {
            padding: 10px;
            flex: 1;
            border-left: 1px solid #ddd;
        }

        .info-row>div:last-child {
            border-left: none;
        }

        .info-row label {
            color: #666;
            font-size: 12px;
            display: block;
        }

        .info-row strong {
            font-size: 14px;
        }

        .section {
            margin: 0;
        }

        .section-title {
            background: #ecf0f1;
            padding: 8px 15px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }

        .section-title.green {
            background: #d5f5e3;
            color: #1e8449;
        }

        .section-title.red {
            background: #fadbd8;
            color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td,
        table th {
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
        }

        table td:last-child {
            text-align: left;
            width: 120px;
        }

        .amount-positive {
            color: #27ae60;
        }

        .amount-negative {
            color: #e74c3c;
        }

        .total-row {
            background: #f8f9fa;
            font-weight: bold;
        }

        .net-salary {
            background: #1a5276;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .net-salary .label {
            font-size: 18px;
        }

        .net-salary .value {
            font-size: 26px;
            font-weight: bold;
        }

        .footer {
            padding: 20px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }

        .print-date {
            text-align: center;
            color: #666;
            font-size: 11px;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }

        @media print {
            body {
                padding: 0;
            }

            .container {
                border: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $payroll->branch->company->name ?? 'شركة الخرسانة' }}</h1>
            <h2>كشف راتب شهري</h2>
        </div>

        <div class="info-row">
            <div>
                <label>اسم الموظف</label>
                <strong>{{ $payroll->employee->name }}</strong>
            </div>
            <div>
                <label>الرقم الوظيفي</label>
                <strong>{{ $payroll->employee->employee_number ?? '-' }}</strong>
            </div>
            <div>
                <label>الفرع</label>
                <strong>{{ $payroll->branch->name ?? '-' }}</strong>
            </div>
        </div>

        <div class="info-row">
            <div>
                <label>الفترة</label>
                <strong>{{ $payroll->period }}</strong>
            </div>
            <div>
                <label>حالة الراتب</label>
                <strong>{{ $payroll->status_name }}</strong>
            </div>
            <div>
                <label>تاريخ الصرف</label>
                <strong>{{ $payroll->paid_at?->format('Y-m-d') ?? 'لم يصرف' }}</strong>
            </div>
        </div>

        <div class="section">
            <div class="section-title">الراتب الأساسي</div>
            <table>
                <tr>
                    <td>الراتب الأساسي الشهري</td>
                    <td><strong>{{ number_format($payroll->basic_salary, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title green">البدلات والإضافات</div>
            <table>
                @if ($payroll->allowances_details)
                    @foreach ($payroll->allowances_details as $allowance)
                        <tr>
                            <td>{{ $allowance['name'] }}</td>
                            <td class="amount-positive">+{{ number_format($allowance['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if ($payroll->bonuses_details)
                    @foreach ($payroll->bonuses_details as $bonus)
                        <tr>
                            <td>{{ $bonus['name'] }}</td>
                            <td class="amount-positive">+{{ number_format($bonus['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if ($payroll->overtime_amount > 0)
                    <tr>
                        <td>أجر إضافي ({{ $payroll->overtime_hours }} ساعة)</td>
                        <td class="amount-positive">+{{ number_format($payroll->overtime_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>إجمالي الإضافات</td>
                    <td class="amount-positive">+{{ number_format($payroll->total_additions, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title red">الخصومات والاستقطاعات</div>
            <table>
                @if ($payroll->deductions_details)
                    @foreach ($payroll->deductions_details as $deduction)
                        <tr>
                            <td>{{ $deduction['name'] }}</td>
                            <td class="amount-negative">-{{ number_format($deduction['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if ($payroll->advances_deducted > 0)
                    <tr>
                        <td>استقطاع سلفة</td>
                        <td class="amount-negative">-{{ number_format($payroll->advances_deducted, 2) }}</td>
                    </tr>
                @endif
                @if ($payroll->absence_deduction > 0)
                    <tr>
                        <td>خصم غياب ({{ $payroll->absence_days }} يوم)</td>
                        <td class="amount-negative">-{{ number_format($payroll->absence_deduction, 2) }}</td>
                    </tr>
                @endif
                @if ($payroll->insurance_deduction > 0)
                    <tr>
                        <td>التأمينات الاجتماعية</td>
                        <td class="amount-negative">-{{ number_format($payroll->insurance_deduction, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>إجمالي الخصومات</td>
                    <td class="amount-negative">-{{ number_format($payroll->total_deductions, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="net-salary">
            <span class="label">صافي الراتب المستحق</span>
            <span class="value">{{ number_format($payroll->net_salary, 2) }} د.ع</span>
        </div>

        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">استلمت المبلغ</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">المحاسب</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">المدير المالي</div>
                </div>
            </div>

            <div class="print-date">
                تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; cursor: pointer;">
            طباعة الكشف
        </button>
    </div>
</body>

</html>
