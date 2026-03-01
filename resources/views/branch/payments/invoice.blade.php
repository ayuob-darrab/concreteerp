<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة دفع #{{ $payment->payment_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background: #fff;
            color: #333;
            font-size: 14px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1a3a5c;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #1a3a5c;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header h2 {
            color: #2563eb;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .invoice-number {
            background: #f0f4ff;
            border: 1px solid #2563eb;
            border-radius: 8px;
            padding: 10px 20px;
            display: inline-block;
            margin-top: 10px;
        }
        .invoice-number strong {
            color: #2563eb;
            font-size: 16px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .info-box {
            flex: 1;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            margin: 0 5px;
        }
        .info-box h4 {
            color: #1a3a5c;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-box p {
            margin-bottom: 5px;
            font-size: 13px;
        }
        .info-box p span {
            font-weight: bold;
            color: #1a3a5c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table th {
            background: #1a3a5c;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 13px;
        }
        table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }
        .totals-box {
            width: 300px;
            border: 2px solid #1a3a5c;
            border-radius: 8px;
            overflow: hidden;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals-row:last-child {
            border-bottom: none;
            background: #1a3a5c;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .totals-row.paid {
            color: #16a34a;
            font-weight: bold;
        }
        .totals-row.remaining {
            color: #dc2626;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid { background: #dcfce7; color: #16a34a; }
        .status-partial { background: #fef3c7; color: #d97706; }
        .status-unpaid { background: #fee2e2; color: #dc2626; }
        .payment-records {
            margin-bottom: 25px;
        }
        .footer {
            text-align: center;
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .invoice-container { padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- زر الطباعة -->
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 30px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                🖨 طباعة الفاتورة
            </button>
            <button onclick="window.close()" style="padding: 10px 30px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px;">
                ✕ إغلاق
            </button>
        </div>

        <!-- رأس الفاتورة -->
        <div class="header">
            <h1>{{ $payment->company->name ?? 'الشركة' }}</h1>
            <h2>فاتورة دفع</h2>
            <p>{{ $payment->branch->branch_name ?? '' }} | {{ $payment->branch->phone ?? '' }}</p>
            <div class="invoice-number">
                <strong>{{ $payment->payment_number }}</strong>
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="info-section">
            <div class="info-box">
                <h4>👤 معلومات الزبون</h4>
                <p>الاسم: <span>{{ $payment->customer_name }}</span></p>
                <p>الهاتف: <span>{{ $payment->customer_phone }}</span></p>
            </div>
            <div class="info-box">
                <h4>📋 معلومات الطلب</h4>
                <p>رقم الطلب: <span>#{{ $payment->work_order_id }}</span></p>
                <p>نوع الخلطة: <span>{{ $payment->workOrder->concreteMix->name ?? '-' }}</span></p>
                <p>الكمية: <span>{{ $payment->workOrder->quantity ?? 0 }} م³</span></p>
            </div>
            <div class="info-box">
                <h4>📅 معلومات الفاتورة</h4>
                <p>التاريخ: <span>{{ $payment->created_at->format('Y-m-d') }}</span></p>
                <p>الحالة:
                    <span class="status-badge status-{{ $payment->status }}">{{ $payment->status_text }}</span>
                </p>
                <p>نوع الدفع: <span>{{ $payment->payment_type_text }}</span></p>
            </div>
        </div>

        <!-- تفاصيل الطلب -->
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->workOrder->concreteMix->name ?? 'خلطة كونكريت' }}</td>
                    <td>{{ $payment->workOrder->quantity ?? 0 }} م³</td>
                    <td>{{ number_format(($payment->workOrder->price ?? $payment->workOrder->initial_price ?? 0), 0) }} دينار</td>
                    <td>{{ number_format($payment->total_amount, 0) }} دينار</td>
                </tr>
            </tbody>
        </table>

        <!-- سجل الدفعات -->
        @if ($payment->records->count() > 0)
            <div class="payment-records">
                <h4 style="margin-bottom: 10px; color: #1a3a5c;">💰 سجل الدفعات</h4>
                <table>
                    <thead>
                        <tr>
                            <th>رقم السجل</th>
                            <th>طريقة الدفع</th>
                            <th>المبلغ</th>
                            <th>المتبقي بعد الدفع</th>
                            <th>بواسطة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payment->records as $record)
                            <tr>
                                <td style="font-family: monospace; font-size: 12px;">{{ $record->record_number }}</td>
                                <td>{{ $record->payment_method_text }}</td>
                                <td style="color: #16a34a; font-weight: bold;">{{ number_format($record->amount, 0) }} دينار</td>
                                <td>{{ number_format($record->balance_after, 0) }} دينار</td>
                                <td>{{ $record->creator->fullname ?? '-' }}</td>
                                <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- المجاميع -->
        <div class="totals-section">
            <div class="totals-box">
                <div class="totals-row">
                    <span>إجمالي المبلغ:</span>
                    <span>{{ number_format($payment->total_amount, 0) }} دينار</span>
                </div>
                <div class="totals-row paid">
                    <span>المبلغ المدفوع:</span>
                    <span>{{ number_format($payment->paid_amount, 0) }} دينار</span>
                </div>
                <div class="totals-row remaining">
                    <span>المبلغ المتبقي:</span>
                    <span>{{ number_format($payment->remaining_amount, 0) }} دينار</span>
                </div>
                <div class="totals-row">
                    <span>الحالة:</span>
                    <span>{{ $payment->status_text }}</span>
                </div>
            </div>
        </div>

        <!-- التوقيعات -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">المحاسب</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">الزبون</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">المدير</div>
            </div>
        </div>

        <!-- تذييل -->
        <div class="footer">
            <p>تم الإصدار بتاريخ {{ now()->format('Y-m-d H:i') }}</p>
            <p>هذه الفاتورة صادرة إلكترونياً من نظام ConcreteERP</p>
        </div>
    </div>
</body>
</html>
