@php
    $car = $maintenance->car;
    $logoPath = $company?->logo ? asset($company->logo) : null;
    $docNo = $maintenance->invoice_number ?: ('CM-' . str_pad((string) $maintenance->id, 6, '0', STR_PAD_LEFT));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة صيانة — {{ $docNo }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
                padding: 0 !important;
            }

            .inv-wrap {
                box-shadow: none !important;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background: #e5e7eb;
            padding: 24px 16px;
            color: #111827;
        }

        .inv-wrap {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .inv-head {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #fff;
            padding: 24px 28px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-box {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f766e;
        }

        .company-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .company-meta {
            font-size: 0.8rem;
            opacity: 0.92;
            line-height: 1.5;
        }

        .doc-badge {
            text-align: left;
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 18px;
            border-radius: 10px;
            min-width: 160px;
        }

        .doc-badge .lbl {
            font-size: 0.75rem;
            opacity: 0.85;
        }

        .doc-badge .val {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .inv-body {
            padding: 28px;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #6b7280;
            margin: 0 0 10px;
            text-transform: none;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 22px;
        }

        @media (max-width: 560px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .doc-badge {
                text-align: right;
                width: 100%;
            }
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 16px;
            background: #f9fafb;
        }

        .card dl {
            margin: 0;
            display: grid;
            gap: 8px;
        }

        .card dt {
            font-size: 0.72rem;
            color: #6b7280;
            font-weight: 600;
        }

        .card dd {
            margin: 0;
            font-weight: 600;
            font-size: 0.95rem;
        }

        table.lines {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 20px;
            font-size: 0.9rem;
        }

        table.lines th {
            background: #f3f4f6;
            padding: 10px 12px;
            text-align: right;
            font-weight: 700;
            border-bottom: 2px solid #e5e7eb;
        }

        table.lines td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        table.lines tr:last-child td {
            border-bottom: none;
        }

        .total-row td {
            font-weight: 800;
            font-size: 1.05rem;
            background: #ecfdf5;
            color: #065f46;
        }

        .pay-box {
            border: 1px dashed #99f6e4;
            background: #f0fdfa;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 8px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            padding: 16px 28px 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-print {
            background: #0d9488;
            color: #fff;
        }

        .btn-back {
            background: #e5e7eb;
            color: #374151;
        }

        .footer-note {
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            padding: 0 28px 20px;
        }
    </style>
</head>

<body>
    @if (session('success'))
        <div class="no-print"
            style="max-width:720px;margin:0 auto 16px;padding:12px 16px;background:#d1fae5;color:#065f46;border-radius:8px;font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    <div class="inv-wrap">
        <header class="inv-head">
            <div class="brand">
                <div class="logo-box">
                    @if ($logoPath)
                        <img src="{{ $logoPath }}" alt="شعار {{ $company->name }}">
                    @else
                        <span class="logo-placeholder">{{ mb_substr($company->name ?? 'شركة', 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <h1 class="company-title">{{ $company->name ?? 'الشركة' }}</h1>
                    <div class="company-meta">
                        @if (!empty($company->phone))
                            <div>📞 {{ $company->phone }}</div>
                        @endif
                        @if (!empty($company->address))
                            <div>📍 {{ $company->address }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="doc-badge">
                <div class="lbl">فاتورة صيانة مركبة</div>
                <div class="val">{{ $docNo }}</div>
                <div class="lbl" style="margin-top:8px;">التاريخ</div>
                <div class="val" style="font-size:0.95rem;">{{ $maintenance->updated_at?->format('Y/m/d H:i') ?? now()->format('Y/m/d H:i') }}</div>
            </div>
        </header>

        <div class="inv-body">
            <p class="section-title">بيانات الفرع والسيارة</p>
            <div class="grid-2">
                <div class="card">
                    <dl>
                        <dt>الفرع</dt>
                        <dd>{{ $maintenance->branch->branch_name ?? '—' }}</dd>
                        <dt>نوع الصيانة</dt>
                        <dd>{{ $maintenance->type_icon }} {{ $maintenance->type_name }}</dd>
                    </dl>
                </div>
                <div class="card">
                    <dl>
                        <dt>السيارة</dt>
                        <dd>{{ $car->car_name ?? $car->car_number }}</dd>
                        <dt>رقم اللوحة / النوع</dt>
                        <dd>{{ $car->car_number }} — {{ $car->carType->name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <p class="section-title">تفاصيل العمل</p>
            <div class="card" style="margin-bottom:18px;">
                <dl>
                    <dt>عنوان الصيانة</dt>
                    <dd>{{ $maintenance->title }}</dd>
                    @if ($maintenance->description)
                        <dt>الوصف</dt>
                        <dd style="font-weight:500;white-space:pre-wrap;">{{ $maintenance->description }}</dd>
                    @endif
                    @if ($maintenance->workshop_name)
                        <dt>الورشة</dt>
                        <dd>{{ $maintenance->workshop_name }}</dd>
                    @endif
                    @if ($maintenance->performed_by)
                        <dt>الفني / المنفّذ</dt>
                        <dd>{{ $maintenance->performed_by }}</dd>
                    @endif
                </dl>
            </div>

            <p class="section-title">المبالغ (دينار عراقي)</p>
            <table class="lines">
                <thead>
                    <tr>
                        <th>البند</th>
                        <th style="width:120px;text-align:center;">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>تكلفة القطع</td>
                        <td style="text-align:center;">{{ number_format((float) ($maintenance->parts_cost ?? 0), 0) }}</td>
                    </tr>
                    <tr>
                        <td>تكلفة العمالة</td>
                        <td style="text-align:center;">{{ number_format((float) ($maintenance->labor_cost ?? 0), 0) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>الإجمالي</td>
                        <td style="text-align:center;">{{ number_format((float) ($maintenance->total_cost ?? 0), 0) }} د.ع</td>
                    </tr>
                </tbody>
            </table>

            <p class="section-title">الدفع</p>
            <div class="pay-box">
                <strong>طريقة الدفع:</strong>
                {{ $maintenance->payment_method_label ?? '—' }}
                @if ($maintenance->payment_method === 'online' && $maintenance->paymentCard)
                    <div style="margin-top:6px;font-size:0.9rem;">
                        البطاقة: {{ $maintenance->paymentCard->card_name }}
                        @if ($maintenance->paymentCard->card_number_masked)
                            ({{ $maintenance->paymentCard->card_number_masked }})
                        @endif
                    </div>
                @endif
                @if ($maintenance->payment_reference)
                    <div style="margin-top:6px;font-size:0.9rem;">المرجع: {{ $maintenance->payment_reference }}</div>
                @endif
            </div>
        </div>

        <div class="actions no-print">
            <button type="button" class="btn btn-print" onclick="window.print()">🖨️ طباعة</button>
            <a href="{{ route('car-maintenance.car-details', $maintenance->car_id) }}" class="btn btn-back">العودة لتفاصيل
                السيارة</a>
        </div>

        <p class="footer-note">وثيقة صادرة إلكترونياً — صيانة السيارات</p>
    </div>

    @if (session('autoprint'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    @endif
</body>

</html>
