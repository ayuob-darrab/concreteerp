@extends('layouts.app')

@section('title', 'تقرير الصندوق اليومي')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الصندوق اليومي</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-cash-register me-2"></i>
                    تقرير الصندوق اليومي
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        @include('reports.partials._filters', [
            'showDateRange' => true,
            'showExport' => true,
            'reportType' => 'dailyCash',
        ])

        {{-- الملخص --}}
        @include('reports.partials._summary-cards', [
            'summaryCards' => [
                [
                    'label' => 'عدد الأيام',
                    'value' => $report['summary']['total_days'] ?? 0,
                    'icon' => 'calendar-alt',
                    'format' => 'number',
                ],
                [
                    'label' => 'إجمالي المقبوضات',
                    'value' => $report['summary']['total_receipts'] ?? 0,
                    'icon' => 'arrow-down',
                    'format' => 'currency',
                    'iconColor' => 'text-success',
                    'iconBg' => 'bg-success',
                ],
                [
                    'label' => 'إجمالي المدفوعات',
                    'value' => $report['summary']['total_payments'] ?? 0,
                    'icon' => 'arrow-up',
                    'format' => 'currency',
                    'iconColor' => 'text-danger',
                    'iconBg' => 'bg-danger',
                ],
                [
                    'label' => 'صافي التدفق',
                    'value' => $report['summary']['net'] ?? 0,
                    'icon' => 'balance-scale',
                    'format' => 'currency',
                    'iconColor' => ($report['summary']['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>حركة الصندوق</h5>
            </div>
            <div class="card-body">
                <canvas id="cashFlowChart" height="100"></canvas>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>ملخصات يومية</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} يوم</span>
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
                            @forelse($report['data'] ?? [] as $summary)
                                <tr>
                                    <td>{{ $summary->summary_date ? \Carbon\Carbon::parse($summary->summary_date)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>{{ number_format($summary->opening_balance ?? 0, 2) }} د.ع</td>
                                    <td class="text-success">{{ number_format($summary->total_receipts ?? 0, 2) }} د.ع</td>
                                    <td class="text-danger">{{ number_format($summary->total_payments ?? 0, 2) }} د.ع</td>
                                    <td class="fw-bold">{{ number_format($summary->closing_balance ?? 0, 2) }} د.ع</td>
                                    <td>
                                        <span class="badge bg-{{ $summary->status == 'closed' ? 'success' : 'warning' }}">
                                            {{ $summary->status == 'closed' ? 'مغلق' : 'مفتوح' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد بيانات في هذه الفترة</p>
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
        const cashData = @json($report['data'] ?? []);

        new Chart(document.getElementById('cashFlowChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: cashData.map(d => d.summary_date),
                datasets: [{
                    label: 'المقبوضات',
                    data: cashData.map(d => d.total_receipts),
                    borderColor: 'rgb(46, 204, 113)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    tension: 0.1
                }, {
                    label: 'المدفوعات',
                    data: cashData.map(d => d.total_payments),
                    borderColor: 'rgb(231, 76, 60)',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.1
                }, {
                    label: 'الرصيد الختامي',
                    data: cashData.map(d => d.closing_balance),
                    borderColor: 'rgb(52, 152, 219)',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
