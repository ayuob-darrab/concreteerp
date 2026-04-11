@php
    $ps = $invoice->payment_status ?? 'pending';
    $statusTheme = match ($ps) {
        'paid' => [
            'header' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
            'banner' => '#d1fae5',
            'bannerText' => '#065f46',
            'label' => 'مسدد بالكامل',
        ],
        'partial' => [
            'header' => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
            'banner' => '#fef3c7',
            'bannerText' => '#92400e',
            'label' => 'سداد جزئي',
        ],
        'overdue' => [
            'header' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
            'banner' => '#fee2e2',
            'bannerText' => '#991b1b',
            'label' => 'متأخر السداد',
        ],
        default => [
            'header' => 'linear-gradient(135deg, #4b5563 0%, #374151 100%)',
            'banner' => '#e5e7eb',
            'bannerText' => '#1f2937',
            'label' => 'قيد الانتظار',
        ],
    };
    $total = (float) ($invoice->total_amount ?? 0);
    $paid = (float) ($invoice->paid_amount ?? 0);
    $remaining = max(0, $total - $paid);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_number }} — {{ $company->name }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
            margin: 0;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-header {
            color: white;
            padding: 28px 36px;
        }

        .status-banner {
            padding: 14px 36px;
            font-weight: 700;
            font-size: 1.05rem;
            text-align: center;
        }

        .invoice-body {
            padding: 32px 36px 40px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px 20px;
            background: #fafafa;
        }

        .card h3 {
            margin: 0 0 14px;
            font-size: 15px;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            border-bottom: 1px dashed #e5e7eb;
        }

        .row:last-child {
            border-bottom: none;
        }

        .muted {
            color: #6b7280;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 14px;
        }

        .details-table th {
            background: #f3f4f6;
            padding: 12px 10px;
            text-align: right;
            border: 1px solid #e5e7eb;
        }

        .details-table td {
            padding: 12px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .totals {
            margin-top: 24px;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            background: #fff;
        }

        .totals .grand {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: 800;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #d1d5db;
            color: #111827;
        }

        .btn-bar {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-print {
            background: #4f46e5;
            color: white;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .footer-note {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px dashed #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header" style="background: {{ $statusTheme['header'] }};">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
                <div>
                    <div style="font-size:13px;opacity:.9;">رقم الفاتورة</div>
                    <div style="font-size:22px;font-weight:800;">{{ $invoice->invoice_number }}</div>
                </div>
                <div style="text-align:left;">
                    <div style="font-size:20px;font-weight:700;">فاتورة اشتراك</div>
                    <div style="font-size:13px;opacity:.9;margin-top:4px;">{{ $invoice->created_at?->format('Y/m/d H:i') }}</div>
                </div>
            </div>
        </div>
        <div class="status-banner" style="background: {{ $statusTheme['banner'] }}; color: {{ $statusTheme['bannerText'] }};">
            حالة السداد: {{ $statusTheme['label'] }}
            @if ($ps === 'partial' && $total > 0)
                — المدفوع {{ number_format($paid, 0) }} د.ع من {{ number_format($total, 0) }} د.ع
            @endif
        </div>

        <div class="invoice-body">
            <div class="btn-bar no-print">
                <button type="button" class="btn btn-print" onclick="window.print()">طباعة</button>
                <a href="{{ route('subscriptions.company-details', $company->code) }}" class="btn btn-back">رجوع لتفاصيل الشركة</a>
            </div>

            <div class="info-grid">
                <div class="card">
                    <h3>الشركة</h3>
                    <div class="row"><span class="muted">الاسم</span><span>{{ $company->name }}</span></div>
                    <div class="row"><span class="muted">الكود</span><span>{{ $company->code }}</span></div>
                    <div class="row"><span class="muted">الهاتف</span><span>{{ $company->phone ?? '—' }}</span></div>
                    <div class="row"><span class="muted">البريد</span><span>{{ $company->email ?? '—' }}</span></div>
                </div>
                <div class="card">
                    <h3>الفترة والنوع</h3>
                    <div class="row"><span class="muted">نوع الفاتورة</span><span>{{ $invoice->type_name ?? $invoice->invoice_type }}</span></div>
                    <div class="row"><span class="muted">من</span><span>{{ $invoice->period_start?->format('Y-m-d') ?? '—' }}</span></div>
                    <div class="row"><span class="muted">إلى</span><span>{{ $invoice->period_end?->format('Y-m-d') ?? '—' }}</span></div>
                    @if ($subscription)
                        <div class="row"><span class="muted">رقم الاشتراك</span><span>#{{ $subscription->id }}</span></div>
                    @endif
                </div>
            </div>

            <table class="details-table">
                <thead>
                    <tr>
                        <th>البيان</th>
                        <th>القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($invoice->invoice_type === 'subscription' || $invoice->invoice_type === 'renewal' || $invoice->invoice_type === 'additional_user')
                        @if ($invoice->users_count)
                            <tr>
                                <td>المستخدمون × سعر الفرد</td>
                                <td>{{ (int) $invoice->users_count }} × {{ number_format((float) ($invoice->price_per_user ?? 0), 0) }} د.ع</td>
                            </tr>
                        @endif
                    @endif
                    @if ($invoice->invoice_type === 'orders_percentage')
                        <tr>
                            <td>طلبات الفترة / إجمالي قيمة الطلبات</td>
                            <td>{{ number_format((int) ($invoice->orders_count ?? 0)) }} — {{ number_format((float) ($invoice->orders_total_value ?? 0), 0) }} د.ع</td>
                        </tr>
                        <tr>
                            <td>نسبة الاستحقاق</td>
                            <td>{{ number_format((float) ($invoice->percentage_rate ?? 0), 2) }}%</td>
                        </tr>
                    @endif
                    <tr>
                        <td>المجموع قبل الخصم</td>
                        <td>{{ number_format((float) ($invoice->subtotal ?? 0), 0) }} د.ع</td>
                    </tr>
                    @if (($invoice->discount ?? 0) > 0)
                        <tr>
                            <td>الخصم</td>
                            <td>{{ number_format((float) $invoice->discount, 0) }} د.ع</td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>الإجمالي المستحق</strong></td>
                        <td><strong>{{ number_format($total, 0) }} د.ع</strong></td>
                    </tr>
                    <tr>
                        <td>المدفوع</td>
                        <td>{{ number_format($paid, 0) }} د.ع</td>
                    </tr>
                    <tr>
                        <td>المتبقي</td>
                        <td>{{ number_format($remaining, 0) }} د.ع</td>
                    </tr>
                    <tr>
                        <td>تاريخ الاستحقاق</td>
                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</td>
                    </tr>
                    @if ($invoice->paid_at)
                        <tr>
                            <td>تاريخ السداد</td>
                            <td>{{ $invoice->paid_at->format('Y-m-d') }}</td>
                        </tr>
                    @endif
                    @if ($invoice->payment_method)
                        <tr>
                            <td>طريقة الدفع</td>
                            <td>{{ $invoice->payment_method }}</td>
                        </tr>
                    @endif
                    @if ($invoice->payment_reference)
                        <tr>
                            <td>مرجع الدفع</td>
                            <td>{{ $invoice->payment_reference }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($invoice->notes)
                <div class="card" style="margin-top:20px;">
                    <h3>ملاحظات</h3>
                    <p style="margin:0;line-height:1.7;color:#374151;">{{ $invoice->notes }}</p>
                </div>
            @endif

            <div class="totals">
                <div class="row grand">
                    <span>صافي الحالة</span>
                    <span>{{ $statusTheme['label'] }}@if ($ps === 'paid') ✓@endif</span>
                </div>
            </div>

            <div class="footer-note">
                {{ $ownerCompany->name ?? 'ConcreteERP' }}
                @if ($ownerCompany && $ownerCompany->phone)
                    — {{ $ownerCompany->phone }}
                @endif
                <br>
                تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}
            </div>
        </div>
    </div>
</body>

</html>
