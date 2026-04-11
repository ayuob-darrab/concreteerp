<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلب عمل #{{ $order->id }}</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; margin: 24px; color: #111; }
        h1 { font-size: 1.25rem; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: right; }
        th { background: #f5f5f5; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <p class="no-print"><a href="{{ route('work-orders.show', $order) }}">رجوع</a> · <button type="button" onclick="window.print()">طباعة</button></p>
    <h1>طلب عمل #{{ $order->id }}</h1>
    <p><strong>الشركة:</strong> {{ $order->company->name ?? $order->company_code }}</p>
    <p><strong>الفرع:</strong> {{ $order->branch->branch_name ?? '—' }}</p>
    <p><strong>الحالة:</strong> {{ $order->status_label ?: '—' }}</p>
    <p><strong>الكمية:</strong> {{ $order->quantity }}</p>
    @if (isset($report['statistics']))
        <p><strong>المنفذ:</strong> {{ $report['statistics']['executed_quantity'] ?? '—' }}</p>
    @endif
    <script>window.onload = function () { /* يمكن تفعيل الطباعة التلقائية: window.print(); */ };</script>
</body>
</html>
