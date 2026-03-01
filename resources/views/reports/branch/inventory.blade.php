@extends('layouts.app')

@section('title', 'تقرير المخزون')

@section('content')
    <div class="container-fluid">
        {{-- العنوان --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير المخزون</li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    تقرير المخزون
                </h2>
            </div>
        </div>

        {{-- الفلاتر --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">حالة المخزون</label>
                            <select name="low_stock" class="form-select">
                                <option value="">جميع المواد</option>
                                <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>مخزون منخفض فقط
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
                            <a href="{{ route('reports.print', ['type' => 'inventory'] + request()->all()) }}"
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
                    'label' => 'إجمالي الأصناف',
                    'value' => $report['summary']['total_items'] ?? 0,
                    'icon' => 'boxes',
                    'format' => 'number',
                ],
                [
                    'label' => 'مخزون منخفض',
                    'value' => $report['summary']['low_stock'] ?? 0,
                    'icon' => 'exclamation-triangle',
                    'format' => 'number',
                    'iconColor' => 'text-danger',
                    'iconBg' => 'bg-danger',
                ],
                [
                    'label' => 'القيمة الإجمالية',
                    'value' => $report['summary']['total_value'] ?? 0,
                    'icon' => 'dollar-sign',
                    'format' => 'currency',
                ],
            ],
        ])

        {{-- الرسم البياني --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>أعلى 10 مواد بالكمية</h5>
            </div>
            <div class="card-body">
                <canvas id="inventoryChart" height="100"></canvas>
            </div>
        </div>

        {{-- جدول البيانات --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>تفاصيل المخزون</h5>
                <span class="badge bg-primary">{{ count($report['data'] ?? []) }} صنف</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                @foreach ($report['columns'] ?? [] as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['data'] ?? [] as $material)
                                @php
                                    $isLowStock = ($material->current_quantity ?? 0) <= ($material->min_quantity ?? 0);
                                @endphp
                                <tr class="{{ $isLowStock ? 'table-danger' : '' }}">
                                    <td>{{ $material->name ?? '-' }}</td>
                                    <td>{{ number_format($material->current_quantity ?? 0) }}</td>
                                    <td>{{ number_format($material->min_quantity ?? 0) }}</td>
                                    <td>{{ $material->unit ?? '-' }}</td>
                                    <td>{{ number_format($material->unit_price ?? 0, 2) }} د.ع</td>
                                    <td>{{ number_format(($material->current_quantity ?? 0) * ($material->unit_price ?? 0), 2) }}
                                        د.ع</td>
                                    <td>
                                        @if ($isLowStock)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                منخفض
                                            </span>
                                        @else
                                            <span class="badge bg-success">طبيعي</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد مواد في المخزون</p>
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
        const materials = @json(collect($report['data'] ?? [])->take(10));
        const ctx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: materials.map(m => m.name),
                datasets: [{
                    label: 'الكمية الحالية',
                    data: materials.map(m => m.current_quantity),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }, {
                    label: 'الحد الأدنى',
                    data: materials.map(m => m.min_quantity),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgb(255, 99, 132)',
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
