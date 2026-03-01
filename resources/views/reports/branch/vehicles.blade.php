@extends('layouts.app')

@section('title', 'تقرير الآليات')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الآليات</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-truck me-2"></i>
                    تقرير الآليات
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>في
                                    الصيانة</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>متوقفة
                                </option>
                            </select>
                        </div>
                        <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>عرض
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </a>
                            <a href="{{ route('reports.print', ['type' => 'vehicles'] + request()->all()) }}"
                                class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-print me-1"></i>طباعة
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- الملخص --}}
        @include('reports.partials._summary-cards', [
            'summaryCards' => [
                [
                    'label' => 'إجمالي الآليات',
                    'value' => $report['summary']['total'] ?? 0,
                    'icon' => 'truck',
                    'format' => 'number',
                ],
                [
                    'label' => 'آليات نشطة',
                    'value' => $report['summary']['active'] ?? 0,
                    'icon' => 'check-circle',
                    'format' => 'number',
                    'iconColor' => 'text-success',
                    'iconBg' => 'bg-success',
                ],
                [
                    'label' => 'في الصيانة',
                    'value' => $report['summary']['in_maintenance'] ?? 0,
                    'icon' => 'tools',
                    'format' => 'number',
                    'iconColor' => 'text-warning',
                    'iconBg' => 'bg-warning',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>توزيع حالة الآليات</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>الآليات حسب النوع</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="typeChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>قائمة الآليات</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} آلية</span>
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
                            @forelse($report['data'] ?? [] as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->plate_number ?? '-' }}</td>
                                    <td>{{ $vehicle->name ?? '-' }}</td>
                                    <td>{{ $vehicle->type ?? '-' }}</td>
                                    <td>{{ $vehicle->model ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'active' => 'success',
                                                'maintenance' => 'warning',
                                                'inactive' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'active' => 'نشطة',
                                                'maintenance' => 'في الصيانة',
                                                'inactive' => 'متوقفة',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$vehicle->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$vehicle->status] ?? $vehicle->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد آليات</p>
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
        const vehicles = @json($report['data'] ?? []);

        // رسم بياني للحالة
        const statusCounts = {
            'نشطة': {{ $report['summary']['active'] ?? 0 }},
            'في الصيانة': {{ $report['summary']['in_maintenance'] ?? 0 }},
            'متوقفة': {{ ($report['summary']['total'] ?? 0) - ($report['summary']['active'] ?? 0) - ($report['summary']['in_maintenance'] ?? 0) }}
        };

        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    data: Object.values(statusCounts),
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
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

        // رسم بياني للنوع
        const typeCounts = {};
        vehicles.forEach(v => {
            typeCounts[v.type || 'غير محدد'] = (typeCounts[v.type || 'غير محدد'] || 0) + 1;
        });

        new Chart(document.getElementById('typeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: Object.keys(typeCounts),
                datasets: [{
                    label: 'عدد الآليات',
                    data: Object.values(typeCounts),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
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
