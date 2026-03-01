@extends('layouts.app')

@section('title', 'طباعة الفاتورة')

@section('content')
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>فاتورة رقم {{ $invoice->invoice_number }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
                font-size: 12px;
                line-height: 1.6;
                color: #333;
                background: #fff;
                direction: rtl;
            }

            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }

            .invoice-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding-bottom: 20px;
                border-bottom: 3px solid #2c3e50;
                margin-bottom: 20px;
            }

            .company-info h1 {
                font-size: 24px;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .company-info p {
                color: #666;
                font-size: 11px;
            }

            .invoice-title {
                text-align: left;
            }

            .invoice-title h2 {
                font-size: 28px;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .invoice-number {
                background: #2c3e50;
                color: #fff;
                padding: 5px 15px;
                border-radius: 4px;
                font-size: 14px;
            }

            .invoice-details {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }

            .bill-to,
            .invoice-info {
                width: 48%;
            }

            .section-title {
                font-size: 14px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #e0e0e0;
            }

            .info-row {
                display: flex;
                justify-content: space-between;
                padding: 5px 0;
            }

            .info-label {
                color: #666;
            }

            .info-value {
                font-weight: 500;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .items-table th {
                background: #2c3e50;
                color: #fff;
                padding: 12px 10px;
                text-align: right;
                font-weight: 500;
            }

            .items-table td {
                padding: 12px 10px;
                border-bottom: 1px solid #e0e0e0;
            }

            .items-table tr:nth-child(even) {
                background: #f9f9f9;
            }

            .items-table .text-center {
                text-align: center;
            }

            .items-table .text-left {
                text-align: left;
            }

            .totals-section {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 30px;
            }

            .totals-table {
                width: 300px;
            }

            .totals-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 10px;
                border-bottom: 1px solid #e0e0e0;
            }

            .totals-row.total {
                background: #2c3e50;
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                border-radius: 4px;
            }

            .payment-info {
                background: #f5f5f5;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
            }

            .payment-info h4 {
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: bold;
            }

            .status-paid {
                background: #27ae60;
                color: #fff;
            }

            .status-unpaid {
                background: #e74c3c;
                color: #fff;
            }

            .status-partial {
                background: #f39c12;
                color: #fff;
            }

            .notes-section {
                background: #fff9e6;
                padding: 15px;
                border-radius: 4px;
                border-right: 4px solid #f39c12;
                margin-bottom: 20px;
            }

            .terms-section {
                font-size: 10px;
                color: #666;
                padding: 15px;
                border-top: 1px solid #e0e0e0;
            }

            .footer {
                text-align: center;
                padding: 20px;
                border-top: 3px solid #2c3e50;
                margin-top: 30px;
            }

            .footer p {
                color: #666;
                font-size: 10px;
            }

            .qr-code {
                text-align: center;
                margin-top: 20px;
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .no-print {
                    display: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="invoice-container">
            {{-- رأس الفاتورة --}}
            <div class="invoice-header">
                <div class="company-info">
                    <h1>{{ $company->name ?? 'اسم الشركة' }}</h1>
                    <p>{{ $company->address ?? '' }}</p>
                    <p>هاتف: {{ $company->phone ?? '' }}</p>
                    <p>الرقم الضريبي: {{ $company->tax_number ?? '' }}</p>
                </div>
                <div class="invoice-title">
                    <h2>فاتورة ضريبية</h2>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                </div>
            </div>

            {{-- تفاصيل الفاتورة والعميل --}}
            <div class="invoice-details">
                <div class="bill-to">
                    <div class="section-title">معلومات العميل</div>
                    <div class="info-row">
                        <span class="info-label">الاسم:</span>
                        <span class="info-value">{{ $invoice->party_name }}</span>
                    </div>
                    @if ($invoice->party_phone)
                        <div class="info-row">
                            <span class="info-label">الهاتف:</span>
                            <span class="info-value">{{ $invoice->party_phone }}</span>
                        </div>
                    @endif
                    @if ($invoice->party_address)
                        <div class="info-row">
                            <span class="info-label">العنوان:</span>
                            <span class="info-value">{{ $invoice->party_address }}</span>
                        </div>
                    @endif
                    @if ($invoice->party_tax_number)
                        <div class="info-row">
                            <span class="info-label">الرقم الضريبي:</span>
                            <span class="info-value">{{ $invoice->party_tax_number }}</span>
                        </div>
                    @endif
                </div>
                <div class="invoice-info">
                    <div class="section-title">معلومات الفاتورة</div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الفاتورة:</span>
                        <span class="info-value">{{ $invoice->invoice_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الاستحقاق:</span>
                        <span class="info-value">{{ $invoice->due_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">حالة الدفع:</span>
                        <span class="info-value">
                            @if ($invoice->status === 'paid')
                                <span class="status-badge status-paid">مدفوعة</span>
                            @elseif($invoice->status === 'partially_paid')
                                <span class="status-badge status-partial">مدفوعة جزئياً</span>
                            @else
                                <span class="status-badge status-unpaid">غير مدفوعة</span>
                            @endif
                        </span>
                    </div>
                    @if ($invoice->workOrder)
                        <div class="info-row">
                            <span class="info-label">رقم الطلب:</span>
                            <span class="info-value">#{{ $invoice->workOrder->id }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- جدول البنود --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 40%">الوصف</th>
                        <th style="width: 10%" class="text-center">الكمية</th>
                        <th style="width: 10%" class="text-center">الوحدة</th>
                        <th style="width: 15%" class="text-left">السعر</th>
                        <th style="width: 20%" class="text-left">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-center">{{ $item->unit ?? 'وحدة' }}</td>
                            <td class="text-left">{{ number_format($item->unit_price, 2) }} د.ع</td>
                            <td class="text-left">{{ number_format($item->total_amount, 2) }} د.ع</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- الإجماليات --}}
            <div class="totals-section">
                <div class="totals-table">
                    <div class="totals-row">
                        <span>المجموع الفرعي:</span>
                        <span>{{ number_format($invoice->subtotal, 2) }} د.ع</span>
                    </div>
                    @if ($invoice->discount_amount > 0)
                        <div class="totals-row">
                            <span>الخصم ({{ $invoice->discount_percentage }}%):</span>
                            <span>- {{ number_format($invoice->discount_amount, 2) }} د.ع</span>
                        </div>
                    @endif
                    <div class="totals-row">
                        <span>ضريبة القيمة المضافة ({{ $invoice->tax_percentage }}%):</span>
                        <span>{{ number_format($invoice->tax_amount, 2) }} د.ع</span>
                    </div>
                    <div class="totals-row total">
                        <span>الإجمالي:</span>
                        <span>{{ number_format($invoice->total_amount, 2) }} د.ع</span>
                    </div>
                    @if ($invoice->paid_amount > 0)
                        <div class="totals-row">
                            <span>المدفوع:</span>
                            <span>{{ number_format($invoice->paid_amount, 2) }} د.ع</span>
                        </div>
                        <div class="totals-row">
                            <span>المتبقي:</span>
                            <span
                                style="color: #e74c3c; font-weight: bold;">{{ number_format($invoice->remaining_amount, 2) }}
                                د.ع</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ملاحظات --}}
            @if ($invoice->notes)
                <div class="notes-section">
                    <strong>ملاحظات:</strong>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif

            {{-- الشروط والأحكام --}}
            @if ($invoice->terms)
                <div class="terms-section">
                    <strong>الشروط والأحكام:</strong>
                    <p>{{ $invoice->terms }}</p>
                </div>
            @endif

            {{-- التذييل --}}
            <div class="footer">
                <p>شكراً لتعاملكم معنا</p>
                <p>تم إصدار هذه الفاتورة إلكترونياً وهي صالحة بدون توقيع أو ختم</p>
                <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
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
