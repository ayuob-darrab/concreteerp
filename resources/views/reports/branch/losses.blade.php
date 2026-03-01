@extends('layouts.app')

@section('title', 'تقرير الخسائر')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الخسائر</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-chart-line-down me-2"></i>
                    تقرير الخسائر
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        @include('reports.partials._filters', [
            'showDateRange' => true,
            'showExport' => true,
            'reportType' => 'losses',
        ])

    @section('extra-filters')
        <div class="col-md-3">
            <label class="form-label">نوع الخسارة</label>
            <select name="loss_type" class="form-select">
                <option value="">جميع الأنواع</option>
                <option value="material" {{ request('loss_type') == 'material' ? 'selected' : '' }}>مواد</option>
                <option value="equipment" {{ request('loss_type') == 'equipment' ? 'selected' : '' }}>معدات</option>
                <option value="vehicle" {{ request('loss_type') == 'vehicle' ? 'selected' : '' }}>آليات</option>
                <option value="other" {{ request('loss_type') == 'other' ? 'selected' : '' }}>أخرى</option>
            </select>
        </div>
    @endsection

    {{-- الملخص --}}
    @include('reports.partials._summary-cards', [
        'summaryCards' => [
            [
                'label' => 'عدد الخسائر',
                'value' => $report['summary']['total_losses'] ?? 0,
                'icon' => 'exclamation-triangle',
                'format' => 'number',
            ],
            [
                'label' => 'إجمالي الخسائر',
                'value' => $report['summary']['total_amount'] ?? 0,
                'icon' => 'money-bill-wave',
                'format' => 'currency',
                'iconColor' => 'text-danger',
                'iconBg' => 'bg-danger',
            ],
        ],
    ])

    {{-- توزيع الخسائر حسب النوع --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>توزيع الخسائر حسب النوع</h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>تطور الخسائر</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول البيانات --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>تفاصيل الخسائر</h5>
            <span class="badge bg-danger">{{ count($report['data'] ?? []) }} خسارة</span>
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
                        @forelse($report['data'] ?? [] as $loss)
                            <tr>
                                <td>{{ $loss->loss_number ?? '-' }}</td>
                                <td>{{ $loss->loss_date ? \Carbon\Carbon::parse($loss->loss_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'material' => 'مواد',
                                            'equipment' => 'معدات',
                                            'vehicle' => 'آليات',
                                            'other' => 'أخرى',
                                        ];
                                    @endphp
                                    {{ $typeLabels[$loss->loss_type] ?? $loss->loss_type }}
                                </td>
                                <td>{{ $loss->description ?? '-' }}</td>
                                <td class="text-danger fw-bold">{{ number_format($loss->loss_amount ?? 0, 2) }} د.ع
                                </td>
                                <td>
                                    <span class="badge bg-{{ $loss->status == 'resolved' ? 'success' : 'warning' }}">
                                        {{ $loss->status == 'resolved' ? 'تمت المعالجة' : 'قيد المعالجة' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p>لا توجد خسائر في هذه الفترة</p>
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
<script>
    const byType = @json($report['summary']['by_type'] ?? []);

    // رسم بياني للنوع
    const typeLabels = {
        'material': 'مواد',
        'equipment': 'معدات',
        'vehicle': 'آليات',
        'other': 'أخرى'
    };

    new Chart(document.getElementById('typeChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: Object.keys(byType).map(k => typeLabels[k] || k),
            datasets: [{
                data: Object.values(byType),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // رسم بياني للتطور
    const losses = @json($report['data'] ?? []);
    const dailyData = {};
    losses.forEach(l => {
        const date = l.loss_date || l.created_at?.split(' ')[0] || 'unknown';
        dailyData[date] = (dailyData[date] || 0) + parseFloat(l.loss_amount || 0);
    });

    const labels = Object.keys(dailyData).sort();

    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'قيمة الخسائر',
                data: labels.map(d => dailyData[d]),
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
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
