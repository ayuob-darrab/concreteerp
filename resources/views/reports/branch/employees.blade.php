@extends('layouts.app')

@section('title', 'تقرير الموظفين')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الموظفين</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    تقرير الموظفين
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
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">القسم</label>
                            <select name="department" class="form-select">
                                <option value="">جميع الأقسام</option>
                                @foreach ($departments ?? [] as $dept)
                                    <option value="{{ $dept }}"
                                        {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>عرض
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </a>
                            <a href="{{ route('reports.print', ['type' => 'employees'] + request()->all()) }}"
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
                    'label' => 'إجمالي الموظفين',
                    'value' => $report['summary']['total'] ?? 0,
                    'icon' => 'users',
                    'format' => 'number',
                ],
                [
                    'label' => 'الموظفون النشطون',
                    'value' => $report['summary']['active'] ?? 0,
                    'icon' => 'user-check',
                    'format' => 'number',
                    'iconColor' => 'text-success',
                    'iconBg' => 'bg-success',
                ],
                [
                    'label' => 'إجمالي الرواتب',
                    'value' => $report['summary']['total_salaries'] ?? 0,
                    'icon' => 'money-bill',
                    'format' => 'currency',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>توزيع الموظفين حسب القسم</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-doughnut me-2"></i>حالة الموظفين</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>قائمة الموظفين</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} موظف</span>
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
                            @forelse($report['data'] ?? [] as $employee)
                                <tr>
                                    <td>{{ $employee->employee_number ?? '-' }}</td>
                                    <td>{{ $employee->name ?? '-' }}</td>
                                    <td>{{ $employee->department ?? '-' }}</td>
                                    <td>{{ $employee->job_title ?? '-' }}</td>
                                    <td>{{ number_format($employee->basic_salary ?? 0, 2) }} د.ع</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $employee->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا يوجد موظفين</p>
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
        const employees = @json($report['data'] ?? []);

        // رسم بياني للأقسام
        const departments = {};
        employees.forEach(e => {
            departments[e.department || 'غير محدد'] = (departments[e.department || 'غير محدد'] || 0) + 1;
        });

        new Chart(document.getElementById('departmentChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: Object.keys(departments),
                datasets: [{
                    data: Object.values(departments),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
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

        // رسم بياني للحالة
        const statusCounts = {
            'نشط': {{ $report['summary']['active'] ?? 0 }},
            'غير نشط': {{ ($report['summary']['total'] ?? 0) - ($report['summary']['active'] ?? 0) }}
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
    </script>
@endpush
