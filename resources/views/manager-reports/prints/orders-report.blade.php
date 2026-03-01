<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير المالي للطلبات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            background: #fff;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* رأس التقرير */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .company-logo {
            width: 70px;
            height: 70px;
            background: #3B82F6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .company-details h1 {
            font-size: 20px;
            color: #1E40AF;
            margin-bottom: 5px;
        }
        
        .company-details p {
            color: #666;
            font-size: 11px;
        }
        
        .report-info {
            text-align: left;
        }
        
        .report-info h2 {
            font-size: 16px;
            color: #3B82F6;
            margin-bottom: 5px;
        }
        
        .report-info .date-range {
            background: #EFF6FF;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 11px;
            color: #1E40AF;
        }
        
        /* بطاقات الملخص */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        
        .summary-card.blue { background: linear-gradient(135deg, #EFF6FF, #DBEAFE); border-color: #93C5FD; }
        .summary-card.green { background: linear-gradient(135deg, #F0FDF4, #DCFCE7); border-color: #86EFAC; }
        .summary-card.purple { background: linear-gradient(135deg, #FAF5FF, #F3E8FF); border-color: #C4B5FD; }
        .summary-card.amber { background: linear-gradient(135deg, #FFFBEB, #FEF3C7); border-color: #FCD34D; }
        
        .summary-card .icon {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .summary-card .value {
            font-size: 22px;
            font-weight: bold;
            color: #1E293B;
        }
        
        .summary-card .label {
            font-size: 10px;
            color: #64748B;
            margin-top: 3px;
        }
        
        /* الجداول */
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background: #1E293B;
            color: white;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th {
            background: #F1F5F9;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            color: #475569;
            border: 1px solid #E2E8F0;
        }
        
        td {
            padding: 8px;
            text-align: center;
            border: 1px solid #E2E8F0;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background: #F8FAFC;
        }
        
        tr:hover {
            background: #EFF6FF;
        }
        
        tfoot td {
            background: #1E293B;
            color: white;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .badge-blue { background: #DBEAFE; color: #1E40AF; }
        .badge-green { background: #DCFCE7; color: #166534; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .badge-red { background: #FEE2E2; color: #991B1B; }
        
        .text-green { color: #16A34A; }
        .text-red { color: #DC2626; }
        .text-blue { color: #2563EB; }
        .text-purple { color: #7C3AED; }
        
        /* توزيع الفروع */
        .branch-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .branch-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px;
        }
        
        .branch-card h4 {
            font-size: 12px;
            color: #1E293B;
            margin-bottom: 8px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 5px;
        }
        
        .branch-card .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        /* تذييل الصفحة */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #64748B;
        }
        
        .signature-area {
            display: flex;
            gap: 60px;
        }
        
        .signature {
            text-align: center;
        }
        
        .signature-line {
            width: 120px;
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        
        /* أنماط الطباعة */
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .container { 
                padding: 10px; 
                max-width: 100%;
            }
            .no-print { display: none; }
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #3B82F6;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .print-btn:hover {
            background: #2563EB;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ طباعة التقرير
    </button>
    
    <div class="container">
        <!-- رأس التقرير -->
        <div class="header">
            <div class="company-info">
                <div class="company-logo">
                    @if($company && $company->logo)
                        <img src="{{ asset($company->logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px;">
                    @else
                        🏢
                    @endif
                </div>
                <div class="company-details">
                    <h1>{{ $company->name ?? 'اسم الشركة' }}</h1>
                    <p>{{ $branch ? 'فرع: ' . $branch->branch_name : 'جميع الفروع' }}</p>
                    <p>تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</p>
                </div>
            </div>
            <div class="report-info">
                <h2>📊 التقرير المالي للطلبات</h2>
                <div class="date-range">
                    من {{ \Carbon\Carbon::parse($fromDate)->format('Y/m/d') }} 
                    إلى {{ \Carbon\Carbon::parse($toDate)->format('Y/m/d') }}
                </div>
            </div>
        </div>
        
        <!-- بطاقات الملخص -->
        <div class="summary-cards">
            <div class="summary-card blue">
                <div class="icon">📋</div>
                <div class="value">{{ number_format($financialStats['total_orders']) }}</div>
                <div class="label">إجمالي الطلبات</div>
            </div>
            <div class="summary-card green">
                <div class="icon">💰</div>
                <div class="value">{{ number_format($financialStats['total_value'], 0) }}</div>
                <div class="label">إجمالي القيمة (د.ع)</div>
            </div>
            <div class="summary-card purple">
                <div class="icon">📦</div>
                <div class="value">{{ number_format($financialStats['total_quantity'], 1) }}</div>
                <div class="label">إجمالي الكميات (م³)</div>
            </div>
            <div class="summary-card amber">
                <div class="icon">📈</div>
                <div class="value">{{ $financialStats['completion_rate'] }}%</div>
                <div class="label">نسبة الإنجاز</div>
            </div>
        </div>
        
        <!-- ملخص الحالات -->
        <div class="section">
            <div class="section-title">
                📊 ملخص حسب الحالة
            </div>
            <table>
                <thead>
                    <tr>
                        <th>الحالة</th>
                        <th>العدد</th>
                        <th>النسبة</th>
                        <th>القيمة (د.ع)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-green">✅ مكتمل</span></td>
                        <td><strong>{{ $financialStats['completed_orders'] }}</strong></td>
                        <td>{{ $financialStats['total_orders'] > 0 ? round(($financialStats['completed_orders'] / $financialStats['total_orders']) * 100, 1) : 0 }}%</td>
                        <td class="text-green"><strong>{{ number_format($financialStats['completed_value'], 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-yellow">⏳ معلق</span></td>
                        <td><strong>{{ $financialStats['pending_orders'] }}</strong></td>
                        <td>{{ $financialStats['total_orders'] > 0 ? round(($financialStats['pending_orders'] / $financialStats['total_orders']) * 100, 1) : 0 }}%</td>
                        <td class="text-blue"><strong>{{ number_format($financialStats['pending_value'], 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-red">❌ ملغي</span></td>
                        <td><strong>{{ $financialStats['cancelled_orders'] }}</strong></td>
                        <td>{{ $financialStats['total_orders'] > 0 ? round(($financialStats['cancelled_orders'] / $financialStats['total_orders']) * 100, 1) : 0 }}%</td>
                        <td>-</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>الإجمالي</td>
                        <td>{{ $financialStats['total_orders'] }}</td>
                        <td>100%</td>
                        <td>{{ number_format($financialStats['total_value'], 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- توزيع الفروع -->
        @if($branchStats->count() > 1)
        <div class="section">
            <div class="section-title">
                🏢 توزيع حسب الفرع
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الفرع</th>
                        <th>عدد الطلبات</th>
                        <th>المكتملة</th>
                        <th>الكمية (م³)</th>
                        <th>القيمة (د.ع)</th>
                        <th>النسبة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branchStats as $index => $stat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $stat['branch_name'] }}</strong></td>
                            <td><span class="badge badge-blue">{{ $stat['count'] }}</span></td>
                            <td><span class="badge badge-green">{{ $stat['completed'] }}</span></td>
                            <td>{{ number_format($stat['quantity'], 1) }}</td>
                            <td class="text-green"><strong>{{ number_format($stat['value'], 0) }}</strong></td>
                            <td>{{ $financialStats['total_value'] > 0 ? round(($stat['value'] / $financialStats['total_value']) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">الإجمالي</td>
                        <td>{{ $financialStats['total_orders'] }}</td>
                        <td>{{ $financialStats['completed_orders'] }}</td>
                        <td>{{ number_format($financialStats['total_quantity'], 1) }}</td>
                        <td>{{ number_format($financialStats['total_value'], 0) }}</td>
                        <td>100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
        
        <!-- تفاصيل الطلبات -->
        <div class="section">
            <div class="section-title">
                📝 تفاصيل الطلبات ({{ $orders->count() }} طلب)
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الفرع</th>
                        <th>العميل</th>
                        <th>الخلطة</th>
                        <th>الكمية</th>
                        <th>السعر (د.ع)</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('m/d') }}</td>
                            <td>{{ $order->branch->branch_name ?? '-' }}</td>
                            <td>{{ Str::limit($order->customer_name, 20) ?? '-' }}</td>
                            <td>{{ $order->concreteMix->name ?? '-' }}</td>
                            <td>{{ number_format($order->quantity, 1) }}</td>
                            <td class="text-green"><strong>{{ number_format($order->final_price ?: $order->initial_price, 0) }}</strong></td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'pending' => 'badge-yellow',
                                        'approved' => 'badge-blue',
                                        'in_progress' => 'badge-blue',
                                        'completed' => 'badge-green',
                                        'delivered' => 'badge-green',
                                        'cancelled' => 'badge-red',
                                    ];
                                    $statusNames = [
                                        'pending' => 'معلق',
                                        'approved' => 'معتمد',
                                        'in_progress' => 'جاري',
                                        'completed' => 'مكتمل',
                                        'delivered' => 'مسلم',
                                        'cancelled' => 'ملغي',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBadges[$order->status] ?? 'badge-blue' }}">
                                    {{ $statusNames[$order->status] ?? $order->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">الإجمالي</td>
                        <td>{{ number_format($financialStats['total_quantity'], 1) }}</td>
                        <td>{{ number_format($financialStats['total_value'], 0) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- تذييل الصفحة -->
        <div class="footer">
            <div>
                <p>تم إنشاء هذا التقرير آلياً بواسطة نظام ConcreteERP</p>
                <p>{{ now()->format('Y/m/d H:i:s') }}</p>
            </div>
            <div class="signature-area">
                <div class="signature">
                    <div class="signature-line">المدير المالي</div>
                </div>
                <div class="signature">
                    <div class="signature-line">مدير الفرع</div>
                </div>
                <div class="signature">
                    <div class="signature-line">المدير العام</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
