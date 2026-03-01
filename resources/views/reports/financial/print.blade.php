<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الطلبات - طباعة</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            padding: 20px;
            font-size: 12px;
            direction: rtl;
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 22px; margin-bottom: 5px; }
        .header p { color: #666; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; background: #f5f5f5; padding: 10px; border-radius: 5px; }
        .info-item { text-align: center; }
        .info-item .label { color: #666; font-size: 11px; }
        .info-item .value { font-weight: bold; font-size: 16px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background: #333; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        tfoot td { background: #eee; font-weight: bold; }
        .status { padding: 3px 8px; border-radius: 10px; font-size: 10px; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in_progress { background: #fff3cd; color: #856404; }
        .status-pending { background: #cce5ff; color: #004085; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .footer { margin-top: 20px; text-align: center; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الطلبات</h1>
        <p>الفترة: {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}</p>
        <p>الفرع: {{ $branchName }}</p>
    </div>

    <div class="info-row">
        <div class="info-item">
            <div class="label">إجمالي الطلبات</div>
            <div class="value">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="info-item">
            <div class="label">إجمالي الكمية</div>
            <div class="value">{{ number_format($stats['total_quantity'], 2) }} م³</div>
        </div>
        <div class="info-item">
            <div class="label">إجمالي المبلغ</div>
            <div class="value">{{ number_format($stats['total_amount'], 2) }} د.ع</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الطلب</th>
                <th>الفرع</th>
                <th>نوع الخلطة</th>
                <th>الكمية</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusLabels = [
                    'new' => 'جديد',
                    'under_review' => 'قيد المراجعة',
                    'approved' => 'معتمد',
                    'in_progress' => 'قيد التنفيذ',
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                ];
            @endphp
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order->order_number ?? $order->id }}</td>
                <td>{{ $order->branch->branch_name ?? '-' }}</td>
                <td>{{ $order->concreteMix->mix_name ?? '-' }}</td>
                <td>{{ number_format($order->quantity, 2) }} م³</td>
                <td>
                    <span class="status status-{{ $order->status_code }}">
                        {{ $statusLabels[$order->status_code] ?? $order->status_code }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                <td>{{ number_format($order->final_price ?? $order->initial_price ?? 0, 2) }} د.ع</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">الإجمالي</td>
                <td>{{ number_format($stats['total_quantity'], 2) }} م³</td>
                <td>{{ $stats['total_orders'] }} طلب</td>
                <td></td>
                <td>{{ number_format($stats['total_amount'], 2) }} د.ع</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        تم إنشاء التقرير بتاريخ {{ now()->format('Y-m-d H:i') }}
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
