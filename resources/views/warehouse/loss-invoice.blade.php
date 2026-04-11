<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة إتلاف #{{ $loss->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; direction: rtl; background: #fff; color: #111827; font-size: 14px; }
        .container { max-width: 820px; margin: 0 auto; padding: 28px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding-bottom: 14px; border-bottom: 2px solid #e5e7eb; margin-bottom: 16px; }
        .brand { display: flex; align-items: center; gap: 12px; min-width: 260px; }
        .logo { width: 64px; height: 64px; border: 1px solid #e5e7eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fff; }
        .logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand h1 { font-size: 18px; margin: 0; }
        .brand .sub { color: #6b7280; font-size: 12px; margin-top: 4px; }
        .doc { text-align: left; }
        .doc .title { font-weight: 800; font-size: 18px; margin-bottom: 6px; }
        .pill { display: inline-block; padding: 6px 12px; border-radius: 999px; background: #fee2e2; color: #991b1b; font-weight: 800; }
        .meta { margin-top: 8px; color: #6b7280; font-size: 12px; line-height: 1.6; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
        .box .k { color: #6b7280; font-size: 12px; margin-bottom: 6px; }
        .box .v { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: center; }
        th { background: #f9fafb; }
        .totals { margin-top: 14px; display: flex; justify-content: flex-end; }
        .totals .box { width: 320px; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e5e7eb; }
        .totals .row:last-child { border-bottom: none; font-size: 16px; font-weight: 800; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 18px; }
        .sig { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; min-height: 92px; display: flex; flex-direction: column; justify-content: space-between; }
        .sig .label { color: #6b7280; font-size: 12px; font-weight: 700; }
        .sig .name { font-weight: 800; margin-top: 6px; }
        .sig .line { border-top: 1px solid #111827; margin-top: 22px; padding-top: 6px; font-size: 12px; color: #6b7280; text-align: center; }
        .no-print { text-align: center; margin-bottom: 14px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; text-decoration: none; color: #fff; font-size: 14px; }
        .btn-print { background: #2563eb; }
        .btn-close { background: #6b7280; margin-right: 8px; }
        @media print { .no-print { display: none; } .container { padding: 12px; } }
    </style>
</head>
<body>
    @php
        $company = $loss->company ?? null;
        $logoPath = $company?->logo ? asset($company->logo) : null;

        $materialNameLower = mb_strtolower((string) ($loss->material_name ?? ''));
        $isCement = str_contains($materialNameLower, 'اسمنت') || str_contains($materialNameLower, 'إسمنت') || str_contains($materialNameLower, 'cement');

        // للأسمنت: عرض الكمية بالأكياس اعتماداً على quantity_base (الأكثر دقة مع اختلاف طرق التخزين السابقة)
        $qtyDisplay = $isCement ? (float) ($loss->quantity_base ?? $loss->quantity_lost) : (float) $loss->quantity_lost;
        $unitLabel = $isCement ? 'كيس' : ($loss->unit ?? '');
        $unitPriceDisplay = $isCement && $qtyDisplay > 0
            ? ((float) $loss->total_cost / (float) $qtyDisplay)
            : (float) $loss->unit_price_display;
    @endphp

    <div class="container">
        <div class="no-print">
            @if (session('success'))
                <p style="color:#16a34a;font-weight:700;margin-bottom:10px;">{{ session('success') }}</p>
            @endif
            <button class="btn btn-print" onclick="window.print()">🖨 طباعة</button>
            <button class="btn btn-close" type="button" onclick="safeClose()">✕ إغلاق</button>
        </div>

        <div class="topbar">
            <div class="brand">
                <div class="logo">
                    @if ($logoPath)
                        <img src="{{ $logoPath }}" alt="Logo">
                    @else
                        <div style="font-weight:800;color:#6b7280;">ERP</div>
                    @endif
                </div>
                <div>
                    <h1>{{ $company?->name ?? 'الشركة' }}</h1>
                    <div class="sub">
                        {{ $loss->branch?->branch_name ?? '' }}
                        @if ($loss->branch?->phone)
                            • {{ $loss->branch->phone }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="doc">
                <div class="title">فاتورة إتلاف مخزون</div>
                <div class="pill">رقم الإتلاف: {{ $loss->id }}</div>
                <div class="meta">
                    التاريخ: {{ ($loss->reported_at ?? $loss->created_at)->format('Y-m-d H:i') }}<br>
                    رقم الشركة: {{ $loss->company_code }}
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="box">
                <div class="k">اسم المادة</div>
                <div class="v">{{ $loss->material_name }}</div>
                <div class="k" style="margin-top:8px;">نوع المادة</div>
                <div class="v">{{ $loss->material_type === 'chemical' ? 'كيميائية' : 'رئيسية' }}</div>
            </div>
            <div class="box">
                <div class="k">من قام بالإتلاف</div>
                <div class="v">{{ $loss->creator?->fullname ?? '—' }}</div>
                <div class="k" style="margin-top:8px;">ملاحظة</div>
                <div class="v" style="font-weight:500;">{{ $loss->note ?: '—' }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>الكمية التالفة</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ rtrim(rtrim(number_format((float) $qtyDisplay, 4, '.', ''), '0'), '.') }}
                        {{ $unitLabel }}
                    </td>
                    <td>{{ number_format((float) $unitPriceDisplay, 0) }} د.ع</td>
                    <td style="font-weight:800;color:#991b1b;">{{ number_format((float) $loss->total_cost, 0) }} د.ع</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="box">
                <div class="row"><span>سعر الوحدة</span><span>{{ number_format((float) $unitPriceDisplay, 0) }} د.ع</span></div>
                <div class="row"><span>الكمية</span><span>{{ rtrim(rtrim(number_format((float) $qtyDisplay, 4, '.', ''), '0'), '.') }} {{ $unitLabel }}</span></div>
                <div class="row"><span>إجمالي مبلغ الإتلاف</span><span>{{ number_format((float) $loss->total_cost, 0) }} د.ع</span></div>
            </div>
        </div>

        <div class="signatures">
            <div class="sig">
                <div>
                    <div class="label">توقيع المتلف</div>
                    <div class="name">{{ $loss->creator?->fullname ?? '—' }}</div>
                </div>
                <div class="line">التوقيع</div>
            </div>
            <div class="sig">
                <div>
                    <div class="label">توقيع المحاسب</div>
                    <div class="name">—</div>
                </div>
                <div class="line">التوقيع</div>
            </div>
            <div class="sig">
                <div>
                    <div class="label">توقيع المدير</div>
                    <div class="name">—</div>
                </div>
                <div class="line">التوقيع</div>
            </div>
        </div>
    </div>

    <script>
        function safeClose() {
            try { window.close(); } catch (e) {}
            if (window && window.closed) return;
            if (window.history && window.history.length > 1) { window.history.back(); return; }
            window.location.href = @json(url('warehouse/addMainMaterialsBranch'));
        }
    </script>
</body>
</html>

