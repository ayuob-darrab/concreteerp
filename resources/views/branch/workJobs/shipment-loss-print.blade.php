<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند تلف شحنة #{{ $loss->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            background: #e5e7eb;
            color: #111827;
            font-size: 13px;
            line-height: 1.45;
        }

        /* معاينة بحجم قريب من A4 على الشاشة */
        .a4-sheet {
            width: 210mm;
            max-width: 100%;
            min-height: 297mm;
            margin: 12px auto;
            padding: 10mm 12mm;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        }

        .no-print {
            text-align: center;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-print {
            background: #2563eb;
        }

        .btn-close {
            background: #6b7280;
            margin-right: 8px;
        }

        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 8px;
            margin-bottom: 10px;
            border-bottom: 2px double #1e3a5f;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }

        .logo {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand h1 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .brand .sub {
            color: #64748b;
            font-size: 11px;
        }

        .doc-id-block {
            text-align: left;
            flex-shrink: 0;
        }

        .doc-id-block .doc-title {
            font-weight: 800;
            font-size: 14px;
            color: #1e3a5f;
            margin-bottom: 4px;
        }

        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            background: #fef3c7;
            color: #92400e;
            font-weight: 800;
            font-size: 11px;
            border: 1px solid #fcd34d;
        }

        .doc-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 10px;
            line-height: 1.5;
        }

        .section {
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #1e3a5f;
            padding: 4px 8px;
            background: #f1f5f9;
            border-radius: 4px 4px 0 0;
            border: 1px solid #e2e8f0;
            border-bottom: none;
        }

        .section-body {
            border: 1px solid #e2e8f0;
            border-radius: 0 0 6px 6px;
            padding: 8px 10px;
            background: #fafafa;
        }

        .kv-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px 12px;
        }

        @media (max-width: 720px) {
            .kv-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .kv .k {
            color: #64748b;
            font-size: 9px;
            margin-bottom: 1px;
        }

        .kv .v {
            font-weight: 700;
            color: #0f172a;
            font-size: 11px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
        }

        table.data th {
            background: #e2e8f0;
            font-weight: 800;
            color: #334155;
        }

        table.data td {
            background: #fff;
            font-weight: 600;
        }

        .notes-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 8px;
            background: #fff;
            margin-top: 6px;
        }

        .notes-box .k {
            color: #64748b;
            font-size: 9px;
            margin-bottom: 2px;
        }

        .notes-box .v {
            font-weight: 500;
            white-space: pre-wrap;
            color: #334155;
            font-size: 10px;
            line-height: 1.35;
        }

        .row-total-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            gap: 8px;
            margin-top: 6px;
        }

        .totals-box {
            border: 2px solid #1e3a5f;
            border-radius: 6px;
            padding: 6px 12px;
            background: #f8fafc;
            flex: 0 0 auto;
            min-width: 200px;
        }

        .totals-box .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 800;
            font-size: 12px;
            color: #0f172a;
        }

        .footer-note {
            flex: 1;
            min-width: 160px;
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
            padding: 4px 0;
        }

        .signatures {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #1e3a5f;
        }

        .signatures-title {
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            color: #1e3a5f;
            margin-bottom: 6px;
        }

        .signatures-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .sign-cell {
            flex: 1;
            text-align: center;
            min-width: 0;
        }

        .sign-cell .role {
            font-weight: 800;
            font-size: 10px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sign-cell .name {
            font-size: 9px;
            color: #475569;
            margin-bottom: 4px;
        }

        .sign-line {
            border-bottom: 1px solid #0f172a;
            height: 28px;
            margin: 0 auto 2px;
            max-width: 220px;
        }

        .sign-cell .hint {
            font-size: 8px;
            color: #94a3b8;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            html,
            body {
                background: #fff;
                height: auto;
            }

            .no-print {
                display: none !important;
            }

            .a4-sheet {
                width: 100%;
                min-height: 0;
                max-height: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            body {
                font-size: 10.5pt;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .doc-header {
                margin-bottom: 6px;
                padding-bottom: 6px;
            }

            .logo {
                width: 44px;
                height: 44px;
            }

            .brand h1 {
                font-size: 12pt;
            }

            .section {
                margin-bottom: 5px;
            }

            .section-body {
                padding: 5px 8px;
            }

            .kv-grid {
                gap: 4px 8px;
            }

            .notes-box .v {
                font-size: 8.5pt;
                max-height: 5em;
                overflow: hidden;
            }

            .sign-line {
                height: 22px;
            }

            .signatures {
                margin-top: 6px;
                padding-top: 6px;
            }
        }
    </style>
</head>

<body>
    @php
        $job = $loss->job;
        $shipment = $loss->shipment;
        $logoPath = $company?->logo ? asset($company->logo) : null;
        $unitPrice = $job ? (float) ($job->unit_price ?? 0) : 0;
        $amount = (float) ($loss->actual_cost ?? $loss->estimated_cost ?? 0);
        $driver =
            $shipment?->mixerDriver ?? $shipment?->truckDriver ?? $shipment?->pumpDriver;
        $driverName = $driver?->fullname ?? '—';
        $branchManager = $loss->branch?->admin;
        $branchManagerName = $branchManager?->fullname ?? '—';
        $mixerLabel = $shipment?->mixer?->car_number ?? '—';
    @endphp

    <div class="no-print">
        <button type="button" class="btn btn-print" onclick="window.print()">🖨 طباعة (A4 صفحة واحدة)</button>
        <button type="button" class="btn btn-close" onclick="window.close()">✕ إغلاق</button>
    </div>

    <div class="a4-sheet">
        <header class="doc-header">
            <div class="brand">
                <div class="logo">
                    @if ($logoPath)
                        <img src="{{ $logoPath }}" alt="Logo">
                    @else
                        <div style="font-weight:800;color:#94a3b8;font-size:10px;">LOGO</div>
                    @endif
                </div>
                <div>
                    <h1>{{ $company?->name ?? 'الشركة' }}</h1>
                    <div class="sub">
                        {{ $loss->branch?->branch_name ?? '—' }}
                        @if ($loss->branch?->phone)
                            — {{ $loss->branch->phone }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="doc-id-block">
                <div class="doc-title">سند تلف شحنة خرسانة</div>
                <div><span class="pill">سند {{ $loss->id }}</span></div>
                <div class="doc-meta">
                    {{ $loss->reported_at?->format('Y-m-d H:i') ?? '—' }}
                    <br>رمز: {{ $loss->company_code }}
                </div>
            </div>
        </header>

        <div class="section">
            <div class="section-title">بيانات أمر العمل — الشحنة — المركبة — السائق</div>
            <div class="section-body">
                <div class="kv-grid">
                    <div class="kv">
                        <div class="k">أمر العمل</div>
                        <div class="v">{{ $job->job_number ?? '—' }}</div>
                    </div>
                    <div class="kv">
                        <div class="k">الشحنة</div>
                        <div class="v">#{{ $shipment->shipment_number ?? '—' }}</div>
                    </div>
                    <div class="kv">
                        <div class="k">نوع الخرسانة</div>
                        <div class="v">{{ $job?->concreteType?->classification ?? '—' }}</div>
                    </div>
                    <div class="kv">
                        <div class="k">نوع التلف</div>
                        <div class="v">{{ \App\Models\WorkLoss::TYPES[$loss->loss_type] ?? $loss->loss_type }}</div>
                    </div>
                    <div class="kv">
                        <div class="k">الخلاطة / الآلية</div>
                        <div class="v">{{ $mixerLabel }}</div>
                    </div>
                    <div class="kv">
                        <div class="k">السائق</div>
                        <div class="v">{{ $driverName }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">الكميات والقيمة</div>
            <div class="section-body" style="background:#fff;padding:0;border-radius:0 0 6px 6px;">
                <table class="data">
                    <thead>
                        <tr>
                            <th>م³ مخطط</th>
                            <th>م³ تلف</th>
                            <th>سعر المتر</th>
                            <th>قيمة التلف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ rtrim(rtrim(number_format((float) ($shipment->planned_quantity ?? 0), 2, '.', ''), '0'), '.') }}
                            </td>
                            <td>{{ rtrim(rtrim(number_format((float) $loss->quantity_lost, 2, '.', ''), '0'), '.') }}</td>
                            <td>{{ number_format($unitPrice, 0) }}</td>
                            <td>{{ number_format($amount, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="notes-box">
            <div class="k">الوصف / الملاحظات</div>
            <div class="v">{{ $loss->description ?: '—' }}</div>
        </div>

        <div class="row-total-footer">
            <div class="totals-box">
                <div class="row">
                    <span>إجمالي التلف</span>
                    <span>{{ number_format($amount, 0) }} د.ع</span>
                </div>
            </div>
            <div class="footer-note">
                <strong>المُبلّغ:</strong> {{ $loss->reportedBy?->fullname ?? '—' }}
                — لا تُحسب كمية التالف ضمن الكمية المنفذة المعتمدة.
            </div>
        </div>

        <div class="signatures">
            <div class="signatures-title">التوقيعات</div>
            <div class="signatures-row">
                <div class="sign-cell">
                    <div class="role">السائق</div>
                    <div class="name">{{ $driverName }}</div>
                    <div class="sign-line"></div>
                    <div class="hint">التوقيع</div>
                </div>
                <div class="sign-cell">
                    <div class="role">مدير الفرع</div>
                    <div class="name">{{ $branchManagerName }}</div>
                    <div class="sign-line"></div>
                    <div class="hint">التوقيع والاعتماد</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
