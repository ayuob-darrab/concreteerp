<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طباعة فاتورة - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #111827; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; font-size: 12px; text-align: right; }
        th { background: #f9fafb; }
        .totals { margin-top: 12px; }
        .totals div { display:flex; justify-content:space-between; padding: 6px 0; }
        .muted { color: #6b7280; font-size: 12px; }
        @media print { .no-print { display:none; } body{ margin:0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px;">
        <button onclick="window.print()">طباعة</button>
    </div>

    <div class="header">
        <div>
            <h2 style="margin:0;">فاتورة</h2>
            <div class="muted">رقم: {{ $invoice->invoice_number }}</div>
        </div>
        <div class="muted">
            <div>تاريخ الفاتورة: {{ optional($invoice->invoice_date)->format('Y-m-d') }}</div>
            <div>الاستحقاق: {{ optional($invoice->due_date)->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="box">
        <div><strong>المقاول:</strong> {{ $invoice->contractor->contract_name ?? $invoice->contractor->name ?? '-' }}</div>
        <div class="muted"><strong>الفرع:</strong> {{ $invoice->branch->name ?? '-' }}</div>
    </div>

    <div class="box">
        <strong>بنود الفاتورة</strong>
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
            @foreach (($invoice->items ?? []) as $item)
                @php
                    $qty = (float) ($item['quantity'] ?? 0);
                    $price = (float) ($item['unit_price'] ?? 0);
                    $rowTotal = $qty * $price;
                @endphp
                <tr>
                    <td>{{ $item['description'] ?? '-' }}</td>
                    <td>{{ number_format($qty, 2) }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>{{ number_format($rowTotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>المجموع الفرعي</span><span>{{ number_format((float) $invoice->subtotal, 2) }}</span></div>
            <div><span>الضريبة</span><span>{{ number_format((float) $invoice->tax_amount, 2) }}</span></div>
            <div><span>الخصم</span><span>{{ number_format((float) $invoice->discount, 2) }}</span></div>
            <div><strong>الإجمالي</strong><strong>{{ number_format((float) $invoice->total, 2) }} د.ع</strong></div>
            <div><span>المدفوع</span><span>{{ number_format((float) $invoice->paid_amount, 2) }} د.ع</span></div>
            <div><strong>المتبقي</strong><strong>{{ number_format((float) $invoice->remaining_amount, 2) }} د.ع</strong></div>
        </div>
    </div>

    @if ($invoice->description)
        <div class="box">
            <strong>الوصف</strong>
            <div class="muted" style="margin-top:6px; white-space:pre-line;">{{ $invoice->description }}</div>
        </div>
    @endif
</body>
</html>

