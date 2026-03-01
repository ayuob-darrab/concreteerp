@extends('layouts.app')

@section('title', 'تقرير الطلبات')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الطلبات</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تقرير الطلبات
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        @include('reports.partials._filters', [
            'showDateRange' => true,
            'showStatusFilter' => true,
            'showExport' => true,
            'reportType' => 'orders',
            'statuses' => [
                'pending' => 'قيد الانتظار',
                'approved' => 'معتمد',
                'in_progress' => 'جاري التنفيذ',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
            ],
        ])

        {{-- الملخص --}}
        @include('reports.partials._summary-cards', [
            'summaryCards' => [
                [
                    'label' => 'إجمالي الطلبات',
                    'value' => $report['summary']['total_orders'] ?? 0,
                    'icon' => 'clipboard-list',
                    'format' => 'number',
                ],
                [
                    'label' => 'إجمالي الكميات',
                    'value' => $report['summary']['total_quantity'] ?? 0,
                    'icon' => 'cubes',
                    'format' => 'number',
                ],
                [
                    'label' => 'إجمالي القيمة',
                    'value' => $report['summary']['total_value'] ?? 0,
                    'icon' => 'money-bill-wave',
                    'format' => 'currency',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>تطور الطلبات</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="100"></canvas>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>تفاصيل الطلبات</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} طلب</span>
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
                            @forelse($report['data'] ?? [] as $order)
                                <tr>
                                    <td>{{ $order->order_number ?? '-' }}</td>
                                    <td>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>{{ $order->customer_name ?? '-' }}</td>
                                    <td>{{ number_format($order->quantity ?? 0) }}</td>
                                    <td>{{ number_format($order->total_price ?? 0, 2) }} د.ع</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'قيد الانتظار',
                                                'approved' => 'معتمد',
                                                'in_progress' => 'جاري التنفيذ',
                                                'completed' => 'مكتمل',
                                                'cancelled' => 'ملغي',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$order->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد طلبات في هذه الفترة</p>
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
        // رسم بياني للطلبات
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($report['summary']['by_status'] ?? []),
                datasets: [{
                    label: 'عدد الطلبات حسب الحالة',
                    data: @json(array_values(($report['summary']['by_status'] ?? [])->toArray())),
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
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
