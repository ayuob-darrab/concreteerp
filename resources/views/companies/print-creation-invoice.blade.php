<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة إنشاء شركة - {{ $company->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            color: #333;
            background: #fff;
            direction: rtl;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
        }

        /* رأس الفاتورة */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 25px;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 30px;
        }

        .company-info h1 {
            font-size: 28px;
            color: #1e40af;
            margin-bottom: 8px;
        }

        .company-info p {
            color: #64748b;
            font-size: 12px;
            margin: 2px 0;
        }

        .invoice-title {
            text-align: left;
        }

        .invoice-title h2 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .invoice-number {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        /* تفاصيل الفاتورة */
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 30px;
        }

        .bill-to,
        .invoice-info {
            width: 48%;
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2563eb;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
        }

        /* جدول البنود */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .items-table th {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 15px;
            text-align: right;
            font-weight: 600;
            font-size: 14px;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            text-align: right;
        }

        .items-table tbody tr:hover {
            background: #f8fafc;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* التذييل */
        .invoice-footer {
            text-align: center;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }

        .invoice-footer p {
            margin: 3px 0;
        }

        /* أزرار الطباعة */
        .print-actions {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #64748b;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        @media print {
            .print-actions {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .invoice-container {
                padding: 0;
                margin: 0;
            }
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- أزرار الطباعة -->
        <div class="print-actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ طباعة الفاتورة
            </button>
            <a href="{{ route('companies.show', 'ListCompanies') }}" class="btn btn-secondary">
                ← العودة للقائمة
            </a>
        </div>

        <!-- رأس الفاتورة -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>🏢 نظام إدارة الخرسانة</h1>
                @php
                    $ownerAddress = $ownerCompany->address ?? 'العراق';
                    $ownerPhone = $ownerCompany->phone ?? '';
                    $ownerEmail = $ownerCompany->email ?? '';
                @endphp
                <p>📍 {{ $ownerAddress }}</p>
                @if ($ownerPhone)
                    <p>📞 هاتف: {{ $ownerPhone }}</p>
                @endif
                @if ($ownerEmail)
                    <p>✉️ {{ $ownerEmail }}</p>
                @endif
            </div>
            <div class="invoice-title">
                <h2>فاتورة إنشاء شركة</h2>
                <span class="invoice-number">INV-COMP-{{ str_pad($company->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- تفاصيل الفاتورة -->
        <div class="invoice-details">
            <div class="bill-to">
                <h4 class="section-title">🏢 بيانات الشركة</h4>
                <div class="info-row">
                    <span class="info-label">اسم الشركة:</span>
                    <span class="info-value">{{ $company->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">كود الشركة:</span>
                    <span class="info-value">{{ $company->code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">اسم المدير:</span>
                    <span class="info-value">{{ $company->managername }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">المحافظة:</span>
                    <span class="info-value">{{ $company->city->name_ar ?? 'غير محدد' }}</span>
                </div>
                @if ($company->phone)
                    <div class="info-row">
                        <span class="info-label">الهاتف:</span>
                        <span class="info-value">{{ $company->phone }}</span>
                    </div>
                @endif
                @if ($company->email)
                    <div class="info-row">
                        <span class="info-label">البريد:</span>
                        <span class="info-value">{{ $company->email }}</span>
                    </div>
                @endif
            </div>

            <div class="invoice-info">
                <h4 class="section-title">📋 تفاصيل الفاتورة</h4>
                <div class="info-row">
                    <span class="info-label">رقم الفاتورة:</span>
                    <span class="info-value">INV-COMP-{{ str_pad($company->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الإصدار:</span>
                    <span class="info-value">{{ $company->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">وقت الإصدار:</span>
                    <span class="info-value">{{ $company->created_at->format('H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الحالة:</span>
                    <span class="info-value">
                        <span class="status-badge status-paid">✅ مدفوعة</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- جدول البنود -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>البيان</th>
                    <th style="width: 100px;">الكمية</th>
                    <th style="width: 150px;">السعر</th>
                    <th style="width: 150px;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>رسوم إنشاء شركة جديدة</strong>
                        <br>
                        <small style="color: #64748b;">إنشاء شركة: {{ $company->name }}</small>
                    </td>
                    <td>1</td>
                    <td>{{ number_format($company->creation_price, 0) }} د.ع</td>
                    <td>{{ number_format($company->creation_price, 0) }} د.ع</td>
                </tr>
            </tbody>
        </table>

        <!-- التذييل -->
        <div class="invoice-footer">
            <p><strong>شكراً لثقتكم بنا</strong></p>
            <p>تم إنشاء هذه الفاتورة آلياً بواسطة نظام إدارة الخرسانة</p>
            <p>📅 {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script>
        // طباعة تلقائية عند تحميل الصفحة
        window.onload = function() {
            // يمكنك تفعيل الطباعة التلقائية بإزالة التعليق
            // window.print();
        };
    </script>
</body>

</html>
