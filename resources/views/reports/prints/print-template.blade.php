<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'تقرير' }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }

            .no-print {
                display: none !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            direction: rtl;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .header .company-name {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .header .date-range {
            font-size: 14px;
            color: #95a5a6;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: right;
        }

        table th {
            background: #2c3e50;
            color: #fff;
            font-weight: 600;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #e9ecef;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }

        .footer .generated-at {
            margin-bottom: 5px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn:hover {
            background: #2980b9;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ طباعة
    </button>

    <div class="container">
        {{-- الترويسة --}}
        <div class="header">
            <div class="company-name">{{ auth()->user()->company->name ?? 'ConcreteERP' }}</div>
            <h1>{{ $title ?? 'تقرير' }}</h1>
            @if (isset($dateFrom) || isset($dateTo))
                <div class="date-range">
                    الفترة: {{ $dateFrom ?? '-' }} إلى {{ $dateTo ?? '-' }}
                </div>
            @endif
        </div>

        {{-- الملخص --}}
        @if (isset($report['summary']))
            <div class="summary">
                @foreach ($report['summary'] as $key => $value)
                    @if (!is_array($value) && !is_object($value))
                        <div class="summary-item">
                            <div class="label">{{ __("reports.summary.{$key}") }}</div>
                            <div class="value">
                                @if (is_numeric($value))
                                    {{ number_format($value, strpos($value, '.') ? 2 : 0) }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- الجدول --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @foreach ($report['columns'] ?? [] as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($report['data'] ?? [] as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        @foreach (array_keys($report['columns'] ?? []) as $key)
                            <td>
                                @php
                                    $value = is_object($row) ? $row->$key ?? '-' : $row[$key] ?? '-';
                                @endphp
                                @if ($key === 'status')
                                    @php
                                        $statusClasses = [
                                            'active' => 'success',
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            'paid' => 'success',
                                            'inactive' => 'secondary',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $statusClasses[$value] ?? 'secondary' }}">
                                        {{ $value }}
                                    </span>
                                @elseif(strpos($key, 'price') !== false ||
                                        strpos($key, 'amount') !== false ||
                                        strpos($key, 'salary') !== false ||
                                        strpos($key, 'balance') !== false ||
                                        strpos($key, 'value') !== false)
                                    {{ number_format($value, 2) }} د.ع
                                @elseif(strpos($key, 'date') !== false || strpos($key, 'at') !== false)
                                    {{ $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-' }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($report['columns'] ?? []) + 1 }}"
                            style="text-align: center; padding: 20px;">
                            لا توجد بيانات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- التذييل --}}
        <div class="footer">
            <div class="generated-at">
                تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}
            </div>
            <div>
                ConcreteERP - نظام إدارة محطات الخرسانة
            </div>
        </div>
    </div>
</body>

</html>
