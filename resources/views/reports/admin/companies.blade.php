@extends('layouts.app')

@section('title', 'تقرير الشركات')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الشركات</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-building me-2"></i>
                    تقرير الشركات
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
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشطة
                                </option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوفة
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="اسم أو كود الشركة"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </a>
                            <a href="{{ route('reports.print', ['type' => 'companies'] + request()->all()) }}"
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
                    'label' => 'إجمالي الشركات',
                    'value' => $report['summary']['total'] ?? 0,
                    'icon' => 'building',
                    'format' => 'number',
                ],
                [
                    'label' => 'شركات نشطة',
                    'value' => $report['summary']['active'] ?? 0,
                    'icon' => 'check-circle',
                    'format' => 'number',
                    'iconColor' => 'text-success',
                    'iconBg' => 'bg-success',
                ],
                [
                    'label' => 'شركات غير نشطة',
                    'value' => $report['summary']['inactive'] ?? 0,
                    'icon' => 'times-circle',
                    'format' => 'number',
                    'iconColor' => 'text-danger',
                    'iconBg' => 'bg-danger',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>توزيع حالة الشركات</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>أكبر الشركات (عدد الفروع)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="branchesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>قائمة الشركات</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} شركة</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                @foreach ($report['columns'] ?? [] as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['data'] ?? [] as $company)
                                <tr>
                                    <td><code>{{ $company->company_code ?? '-' }}</code></td>
                                    <td>{{ $company->name ?? '-' }}</td>
                                    <td>{{ $company->branches_count ?? 0 }}</td>
                                    <td>{{ $company->users_count ?? 0 }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'suspended' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'active' => 'نشطة',
                                                'inactive' => 'غير نشطة',
                                                'suspended' => 'موقوفة',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$company->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$company->status] ?? $company->status }}
                                        </span>
                                    </td>
                                    <td>{{ $company->created_at ? \Carbon\Carbon::parse($company->created_at)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد شركات</p>
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
        // رسم بياني للحالة
        const statusCounts = {
            'نشطة': {{ $report['summary']['active'] ?? 0 }},
            'غير نشطة': {{ $report['summary']['inactive'] ?? 0 }}
        };

        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    data: Object.values(statusCounts),
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
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

        // رسم بياني للفروع
        const companies = @json(collect($report['data'] ?? [])->sortByDesc('branches_count')->take(10)->values());

        new Chart(document.getElementById('branchesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: companies.map(c => c.name),
                datasets: [{
                    label: 'عدد الفروع',
                    data: companies.map(c => c.branches_count),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
