<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>أمر عمل #{{ $job->job_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #333;
        }

        .title {
            font-size: 18pt;
            margin-top: 10px;
        }

        .job-number {
            font-size: 14pt;
            color: #666;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background: #f5f5f5;
            padding: 8px 15px;
            font-weight: bold;
            border-right: 4px solid #333;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: right;
        }

        table th {
            background: #f9f9f9;
            font-weight: bold;
            width: 35%;
        }

        .two-col {
            display: flex;
            gap: 20px;
        }

        .two-col>div {
            flex: 1;
        }

        .materials-table th {
            background: #e9ecef;
            text-align: center;
        }

        .materials-table td {
            text-align: center;
        }

        .notes {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
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

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- زر الطباعة -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer;">
            🖨️ طباعة
        </button>
    </div>

    <!-- الرأس -->
    <div class="header">
        <div class="logo">{{ config('app.name', 'Concrete ERP') }}</div>
        <div class="title">أمر عمل</div>
        <div class="job-number">رقم: {{ $job->job_number }}</div>
    </div>

    <!-- معلومات الأمر والعميل -->
    <div class="two-col">
        <div class="section">
            <div class="section-title">معلومات أمر العمل</div>
            <table>
                <tr>
                    <th>رقم الأمر:</th>
                    <td>{{ $job->job_number }}</td>
                </tr>
                <tr>
                    <th>التاريخ:</th>
                    <td>{{ $job->scheduled_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>الوقت المجدول:</th>
                    <td>{{ $job->scheduled_time ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <th>رقم الطلب:</th>
                    <td>{{ $job->order->order_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th>الحالة:</th>
                    <td>{{ $job->status_label }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">معلومات العميل</div>
            <table>
                <tr>
                    <th>اسم العميل:</th>
                    <td>{{ $job->customer_name }}</td>
                </tr>
                <tr>
                    <th>رقم الجوال:</th>
                    <td>{{ $job->customer_phone }}</td>
                </tr>
                <tr>
                    <th>اسم المشروع:</th>
                    <td>{{ $job->project_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>العنوان:</th>
                    <td>{{ $job->location_address ?? '-' }}</td>
                </tr>
                <tr>
                    <th>المشرف:</th>
                    <td>{{ $job->supervisor->name ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- تفاصيل العمل -->
    <div class="section">
        <div class="section-title">تفاصيل العمل</div>
        <table>
            <tr>
                <th>نوع الخرسانة:</th>
                <td>{{ $job->concreteType->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>الكمية المطلوبة:</th>
                <td>{{ number_format($job->required_quantity, 1) }} م³</td>
            </tr>
            <tr>
                <th>السعر للمتر:</th>
                <td>{{ number_format($job->unit_price, 2) }} د.ع</td>
            </tr>
            <tr>
                <th>الإجمالي:</th>
                <td>{{ number_format($job->total_price, 2) }} د.ع</td>
            </tr>
        </table>
    </div>

    <!-- المواد المحجوزة -->
    @if ($job->materialReservations && $job->materialReservations->count() > 0)
        <div class="section">
            <div class="section-title">المواد المحجوزة</div>
            <table class="materials-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المادة</th>
                        <th>الكمية المحجوزة</th>
                        <th>الكمية المستخدمة</th>
                        <th>الوحدة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($job->materialReservations as $index => $reservation)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $reservation->material->name ?? '-' }}</td>
                            <td>{{ number_format($reservation->reserved_quantity, 2) }}</td>
                            <td>{{ number_format($reservation->used_quantity, 2) }}</td>
                            <td>{{ $reservation->material->unit ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- ملاحظات -->
    @if ($job->notes)
        <div class="section">
            <div class="section-title">ملاحظات</div>
            <div class="notes">
                {{ $job->notes }}
            </div>
        </div>
    @endif

    <!-- التوقيعات -->
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">مسؤول الإنتاج</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">المشرف</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">العميل</div>
        </div>
    </div>

    <!-- التذييل -->
    <div class="footer">
        <small>
            تم الإنشاء بتاريخ: {{ $job->created_at->format('Y-m-d H:i') }}
            |
            الفرع: {{ $job->branch->name ?? '-' }}
        </small>
    </div>
</body>

</html>
