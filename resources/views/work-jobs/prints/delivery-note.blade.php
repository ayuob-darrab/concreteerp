<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إذن تسليم #{{ $shipment->shipment_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            padding: 15px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
        }

        .doc-info {
            text-align: left;
        }

        .doc-title {
            font-size: 16pt;
            font-weight: bold;
            color: #333;
            background: #f5f5f5;
            padding: 5px 15px;
        }

        .doc-number {
            font-size: 12pt;
            margin-top: 5px;
        }

        .content {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .content>div {
            flex: 1;
        }

        .box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .box-title {
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            color: #666;
        }

        .quantity-box {
            text-align: center;
            background: #e8f4e8;
            border: 2px solid #28a745;
            padding: 15px;
            border-radius: 10px;
        }

        .quantity-value {
            font-size: 28pt;
            font-weight: bold;
            color: #28a745;
        }

        .quantity-label {
            font-size: 10pt;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background: #f5f5f5;
        }

        .times-table {}

        .times-table th {
            width: 25%;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 15px;
        }

        .signature-box {
            text-align: center;
            width: 22%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10pt;
        }

        .notes {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .footer {
            font-size: 9pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
        }

        .copy-label {
            text-align: center;
            font-size: 10pt;
            color: #999;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <!-- زر الطباعة -->
    <div class="no-print" style="text-align: center; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 25px; font-size: 12pt; cursor: pointer;">
            🖨️ طباعة
        </button>
    </div>

    <!-- نسخة المكتب -->
    <div class="copy-label">نسخة المكتب</div>

    <!-- الرأس -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <small>{{ $shipment->branch->name ?? '' }}</small>
        </div>
        <div class="doc-info">
            <div class="doc-title">إذن تسليم</div>
            <div class="doc-number">#{{ $shipment->shipment_number }}</div>
            <small>{{ $shipment->created_at->format('Y-m-d') }}</small>
        </div>
    </div>

    <!-- المحتوى -->
    <div class="content">
        <!-- معلومات العميل -->
        <div class="box">
            <div class="box-title">العميل</div>
            <div class="info-row">
                <span class="info-label">الاسم:</span>
                <span>{{ $shipment->job->customer_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الجوال:</span>
                <span>{{ $shipment->job->customer_phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">المشروع:</span>
                <span>{{ $shipment->job->project_name ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">العنوان:</span>
                <span>{{ $shipment->job->location_address ?? '-' }}</span>
            </div>
        </div>

        <!-- معلومات المنتج -->
        <div class="box">
            <div class="box-title">المنتج</div>
            <div class="info-row">
                <span class="info-label">أمر العمل:</span>
                <span>{{ $shipment->job->job_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">نوع الخرسانة:</span>
                <span>{{ $shipment->job->concreteType->name ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">السعر/م³:</span>
                <span>{{ number_format($shipment->job->unit_price, 2) }} د.ع</span>
            </div>
        </div>

        <!-- الكمية -->
        <div class="quantity-box">
            <div class="quantity-value">
                {{ number_format($shipment->actual_quantity ?? $shipment->planned_quantity, 1) }}</div>
            <div class="quantity-label">متر مكعب</div>
        </div>
    </div>

    <!-- الآليات والسائقين -->
    <table>
        <thead>
            <tr>
                <th>الآلية</th>
                <th>رقم اللوحة</th>
                <th>السائق</th>
                <th>الجوال</th>
            </tr>
        </thead>
        <tbody>
            @if ($shipment->mixer)
                <tr>
                    <td>الخلاطة</td>
                    <td>{{ $shipment->mixer->plate_number }}</td>
                    <td>{{ $shipment->mixerDriver->name ?? '-' }}</td>
                    <td>{{ $shipment->mixerDriver->phone ?? '-' }}</td>
                </tr>
            @endif
            @if ($shipment->truck)
                <tr>
                    <td>اللوري</td>
                    <td>{{ $shipment->truck->plate_number }}</td>
                    <td>{{ $shipment->truckDriver->name ?? '-' }}</td>
                    <td>{{ $shipment->truckDriver->phone ?? '-' }}</td>
                </tr>
            @endif
            @if ($shipment->pump)
                <tr>
                    <td>المضخة</td>
                    <td>{{ $shipment->pump->plate_number }}</td>
                    <td>{{ $shipment->pumpDriver->name ?? '-' }}</td>
                    <td>{{ $shipment->pumpDriver->phone ?? '-' }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- الأوقات -->
    <table class="times-table">
        <thead>
            <tr>
                <th>الانطلاق</th>
                <th>الوصول</th>
                <th>بدء العمل</th>
                <th>انتهاء العمل</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $shipment->departure_time ? $shipment->departure_time->format('H:i') : '____' }}</td>
                <td>{{ $shipment->arrival_time ? $shipment->arrival_time->format('H:i') : '____' }}</td>
                <td>{{ $shipment->work_start_time ? $shipment->work_start_time->format('H:i') : '____' }}</td>
                <td>{{ $shipment->work_end_time ? $shipment->work_end_time->format('H:i') : '____' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- الملاحظات -->
    @if ($shipment->notes)
        <div class="notes">
            <strong>ملاحظات:</strong> {{ $shipment->notes }}
        </div>
    @endif

    <!-- التوقيعات -->
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">مسؤول الإنتاج</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">السائق</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">العميل</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">المحاسب</div>
        </div>
    </div>

    <!-- التذييل -->
    <div class="footer">
        هذا الإذن صادر من نظام {{ config('app.name', 'Concrete ERP') }} | رقم الشحنة: {{ $shipment->id }}
    </div>

    <!-- فاصل صفحة - نسخة العميل -->
    <div class="page-break"></div>
    <div class="copy-label">نسخة العميل</div>

    <!-- تكرار المحتوى لنسخة العميل -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ config('app.name', 'Concrete ERP') }}</div>
            <small>{{ $shipment->branch->name ?? '' }}</small>
        </div>
        <div class="doc-info">
            <div class="doc-title">إذن تسليم</div>
            <div class="doc-number">#{{ $shipment->shipment_number }}</div>
            <small>{{ $shipment->created_at->format('Y-m-d') }}</small>
        </div>
    </div>

    <div class="content">
        <div class="box">
            <div class="box-title">العميل</div>
            <div class="info-row">
                <span class="info-label">الاسم:</span>
                <span>{{ $shipment->job->customer_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">أمر العمل:</span>
                <span>{{ $shipment->job->job_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">نوع الخرسانة:</span>
                <span>{{ $shipment->job->concreteType->name ?? '-' }}</span>
            </div>
        </div>

        <div class="quantity-box">
            <div class="quantity-value">
                {{ number_format($shipment->actual_quantity ?? $shipment->planned_quantity, 1) }}</div>
            <div class="quantity-label">متر مكعب</div>
            <div style="margin-top: 10px; font-size: 12pt;">
                <strong>{{ number_format(($shipment->actual_quantity ?? $shipment->planned_quantity) * $shipment->job->unit_price, 2) }}</strong>
                د.ع
            </div>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">العميل</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">السائق</div>
        </div>
    </div>

    <div class="footer">
        شكراً لتعاملكم معنا | {{ config('app.name', 'Concrete ERP') }}
    </div>
</body>

</html>
