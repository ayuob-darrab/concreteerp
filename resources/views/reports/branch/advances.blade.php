@extends('layouts.app')

@section('title', 'تقرير السلف')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير السلف</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-hand-holding-usd me-2"></i>
                    تقرير السلف
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        @include('reports.partials._filters', [
            'showDateRange' => true,
            'showStatusFilter' => true,
            'showExport' => true,
            'reportType' => 'advances',
            'statuses' => [
                'active' => 'نشطة',
                'paid' => 'مسددة',
                'cancelled' => 'ملغاة',
            ],
        ])

        {{-- الملخص --}}
        @include('reports.partials._summary-cards', [
            'summaryCards' => [
                [
                    'label' => 'إجمالي السلف',
                    'value' => $report['summary']['total_advances'] ?? 0,
                    'icon' => 'file-invoice-dollar',
                    'format' => 'number',
                ],
                [
                    'label' => 'إجمالي المبالغ',
                    'value' => $report['summary']['total_amount'] ?? 0,
                    'icon' => 'money-bill-wave',
                    'format' => 'currency',
                ],
                [
                    'label' => 'المبالغ المتبقية',
                    'value' => $report['summary']['total_remaining'] ?? 0,
                    'icon' => 'hourglass-half',
                    'format' => 'currency',
                    'iconColor' => 'text-warning',
                    'iconBg' => 'bg-warning',
                ],
                [
                    'label' => 'سلف نشطة',
                    'value' => $report['summary']['active'] ?? 0,
                    'icon' => 'check-circle',
                    'format' => 'number',
                    'iconColor' => 'text-success',
                    'iconBg' => 'bg-success',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>تطور السلف خلال الفترة</h5>
            </div>
            <div class="card-body">
                <canvas id="advancesChart" height="100"></canvas>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>تفاصيل السلف</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} سلفة</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                @foreach ($report['columns'] ?? [] as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['data'] ?? [] as $advance)
                                <tr>
                                    <td>{{ $advance->advance_number ?? '-' }}</td>
                                    <td>{{ $advance->created_at ? \Carbon\Carbon::parse($advance->created_at)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>{{ $advance->employee_name ?? '-' }}</td>
                                    <td>{{ number_format($advance->amount ?? 0, 2) }} د.ع</td>
                                    <td>{{ number_format($advance->remaining_amount ?? 0, 2) }} د.ع</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'active' => 'warning',
                                                'paid' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'active' => 'نشطة',
                                                'paid' => 'مسددة',
                                                'cancelled' => 'ملغاة',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$advance->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$advance->status] ?? $advance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد سلف في هذه الفترة</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script>
        const advances = @json($report['data'] ?? []);

        // تجميع البيانات حسب التاريخ
        const dailyData = {};
        advances.forEach(a => {
            const date = a.created_at ? a.created_at.split(' ')[0] : 'unknown';
            if (!dailyData[date]) {
                dailyData[date] = {
                    amount: 0,
                    count: 0
                };
            }
            dailyData[date].amount += parseFloat(a.amount || 0);
            dailyData[date].count++;
        });

        const labels = Object.keys(dailyData).sort();
        const amounts = labels.map(d => dailyData[d].amount);

        new Chart(document.getElementById('advancesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'إجمالي السلف',
                    data: amounts,
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
