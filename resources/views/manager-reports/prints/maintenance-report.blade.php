<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير المالي لصيانة السيارات</title>
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
            border-bottom: 3px solid #EA580C;
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
            background: linear-gradient(135deg, #EA580C, #DC2626);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .company-details h1 {
            font-size: 20px;
            color: #C2410C;
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
            color: #EA580C;
            margin-bottom: 5px;
        }
        
        .report-info .date-range {
            background: #FFF7ED;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 11px;
            color: #C2410C;
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
        
        .summary-card.orange { background: linear-gradient(135deg, #FFF7ED, #FFEDD5); border-color: #FDBA74; }
        .summary-card.red { background: linear-gradient(135deg, #FEF2F2, #FEE2E2); border-color: #FCA5A5; }
        .summary-card.purple { background: linear-gradient(135deg, #FAF5FF, #F3E8FF); border-color: #C4B5FD; }
        .summary-card.green { background: linear-gradient(135deg, #F0FDF4, #DCFCE7); border-color: #86EFAC; }
        
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
        
        /* تفصيل التكاليف */
        .cost-breakdown {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .cost-card {
            background: #F8FAFC;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border-left: 4px solid;
        }
        
        .cost-card.parts { border-color: #8B5CF6; }
        .cost-card.labor { border-color: #22C55E; }
        .cost-card.total { border-color: #EF4444; }
        
        .cost-card .label {
            font-size: 11px;
            color: #64748B;
            margin-bottom: 5px;
        }
        
        .cost-card .value {
            font-size: 20px;
            font-weight: bold;
        }
        
        .cost-card.parts .value { color: #8B5CF6; }
        .cost-card.labor .value { color: #22C55E; }
        .cost-card.total .value { color: #EF4444; }
        
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
            background: #FFF7ED;
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
        .badge-orange { background: #FFEDD5; color: #C2410C; }
        .badge-purple { background: #F3E8FF; color: #6B21A8; }
        
        .text-green { color: #16A34A; }
        .text-red { color: #DC2626; }
        .text-blue { color: #2563EB; }
        .text-purple { color: #7C3AED; }
        .text-orange { color: #EA580C; }
        
        /* أنواع الصيانة */
        .maintenance-types {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .type-card {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        
        .type-card .icon {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .type-card .name {
            font-size: 10px;
            color: #64748B;
            margin-bottom: 3px;
        }
        
        .type-card .count {
            font-size: 16px;
            font-weight: bold;
            color: #1E293B;
        }
        
        .type-card .cost {
            font-size: 10px;
            color: #EF4444;
            margin-top: 3px;
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
            background: #EA580C;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
        }
        
        .print-btn:hover {
            background: #C2410C;
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
                        🔧
                    @endif
                </div>
                <div class="company-details">
                    <h1>{{ $company->name ?? 'اسم الشركة' }}</h1>
                    <p>{{ $branch ? 'فرع: ' . $branch->branch_name : 'جميع الفروع' }}</p>
                    <p>تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</p>
                </div>
            </div>
            <div class="report-info">
                <h2>🔧 التقرير المالي للصيانة</h2>
                <div class="date-range">
                    من {{ \Carbon\Carbon::parse($fromDate)->format('Y/m/d') }} 
                    إلى {{ \Carbon\Carbon::parse($toDate)->format('Y/m/d') }}
                </div>
            </div>
        </div>
        
        <!-- بطاقات الملخص -->
        <div class="summary-cards">
            <div class="summary-card orange">
                <div class="icon">🔧</div>
                <div class="value">{{ number_format($financialStats['total_maintenances']) }}</div>
                <div class="label">عدد الصيانات</div>
            </div>
            <div class="summary-card red">
                <div class="icon">💰</div>
                <div class="value">{{ number_format($financialStats['total_cost'], 0) }}</div>
                <div class="label">إجمالي التكاليف (د.ع)</div>
            </div>
            <div class="summary-card purple">
                <div class="icon">🚗</div>
                <div class="value">{{ $financialStats['unique_cars'] }}</div>
                <div class="label">عدد السيارات</div>
            </div>
            <div class="summary-card green">
                <div class="icon">📊</div>
                <div class="value">{{ number_format($financialStats['average_cost'], 0) }}</div>
                <div class="label">متوسط التكلفة (د.ع)</div>
            </div>
        </div>
        
        <!-- تفصيل التكاليف -->
        <div class="cost-breakdown">
            <div class="cost-card parts">
                <div class="label">⚙️ تكلفة القطع والمواد</div>
                <div class="value">{{ number_format($financialStats['parts_cost'], 2) }} د.ع</div>
            </div>
            <div class="cost-card labor">
                <div class="label">👷 تكلفة العمالة</div>
                <div class="value">{{ number_format($financialStats['labor_cost'], 2) }} د.ع</div>
            </div>
            <div class="cost-card total">
                <div class="label">💵 الإجمالي الكلي</div>
                <div class="value">{{ number_format($financialStats['total_cost'], 2) }} د.ع</div>
            </div>
        </div>
        
        <!-- توزيع أنواع الصيانة -->
        <div class="section">
            <div class="section-title">
                🔧 توزيع حسب نوع الصيانة
            </div>
            <table>
                <thead>
                    <tr>
                        <th>النوع</th>
                        <th>العدد</th>
                        <th>النسبة</th>
                        <th>إجمالي التكلفة (د.ع)</th>
                        <th>متوسط التكلفة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($typeStats as $type => $stat)
                        <tr>
                            <td>
                                <span class="badge badge-orange">{{ $stat['icon'] }} {{ $stat['name'] }}</span>
                            </td>
                            <td><strong>{{ $stat['count'] }}</strong></td>
                            <td>{{ $financialStats['total_maintenances'] > 0 ? round(($stat['count'] / $financialStats['total_maintenances']) * 100, 1) : 0 }}%</td>
                            <td class="text-red"><strong>{{ number_format($stat['total_cost'], 0) }}</strong></td>
                            <td>{{ $stat['count'] > 0 ? number_format($stat['total_cost'] / $stat['count'], 0) : 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>الإجمالي</td>
                        <td>{{ $financialStats['total_maintenances'] }}</td>
                        <td>100%</td>
                        <td>{{ number_format($financialStats['total_cost'], 0) }}</td>
                        <td>{{ number_format($financialStats['average_cost'], 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- أعلى السيارات تكلفة -->
        @if($carStats->count() > 0)
        <div class="section">
            <div class="section-title">
                🚗 أعلى السيارات تكلفة في الصيانة
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>السيارة</th>
                        <th>النوع</th>
                        <th>رقم اللوحة</th>
                        <th>عدد الصيانات</th>
                        <th>إجمالي التكلفة</th>
                        <th>آخر صيانة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carStats as $index => $stat)
                        <tr>
                            <td>
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </td>
                            <td><strong>{{ $stat['car_name'] }}</strong></td>
                            <td>{{ $stat['car_type'] }}</td>
                            <td><span class="badge badge-blue">{{ $stat['plate_number'] }}</span></td>
                            <td><span class="badge badge-orange">{{ $stat['count'] }}</span></td>
                            <td class="text-red"><strong>{{ number_format($stat['total_cost'], 0) }} د.ع</strong></td>
                            <td>{{ $stat['last_maintenance'] ? \Carbon\Carbon::parse($stat['last_maintenance'])->format('Y/m/d') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- تفاصيل الصيانات -->
        <div class="section">
            <div class="section-title">
                📝 تفاصيل الصيانات ({{ $maintenances->count() }} سجل)
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>السيارة</th>
                        <th>النوع</th>
                        <th>الوصف</th>
                        <th>القطع</th>
                        <th>العمالة</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenances as $index => $maintenance)
                        @php
                            $typeInfo = $maintenanceTypes[$maintenance->maintenance_type] ?? ['name' => '-', 'icon' => '🔧'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('m/d') }}</td>
                            <td><strong>{{ $maintenance->car->car_name ?? $maintenance->car->car_number ?? '-' }}</strong></td>
                            <td>{{ $typeInfo['icon'] }}</td>
                            <td>{{ Str::limit($maintenance->title, 25) }}</td>
                            <td class="text-purple">{{ number_format($maintenance->parts_cost, 0) }}</td>
                            <td class="text-green">{{ number_format($maintenance->labor_cost, 0) }}</td>
                            <td class="text-red"><strong>{{ number_format($maintenance->total_cost, 0) }}</strong></td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'scheduled' => 'badge-yellow',
                                        'in_progress' => 'badge-blue',
                                        'completed' => 'badge-green',
                                        'cancelled' => 'badge-red',
                                    ];
                                    $statusNames = [
                                        'scheduled' => 'مجدولة',
                                        'in_progress' => 'جارية',
                                        'completed' => 'مكتملة',
                                        'cancelled' => 'ملغية',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBadges[$maintenance->status] ?? 'badge-blue' }}">
                                    {{ $statusNames[$maintenance->status] ?? $maintenance->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">الإجمالي</td>
                        <td>{{ number_format($financialStats['parts_cost'], 0) }}</td>
                        <td>{{ number_format($financialStats['labor_cost'], 0) }}</td>
                        <td>{{ number_format($financialStats['total_cost'], 0) }}</td>
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
                    <div class="signature-line">مسؤول الصيانة</div>
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
