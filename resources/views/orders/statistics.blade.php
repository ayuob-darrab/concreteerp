@extends('layouts.app')

@section('title', 'إحصائيات الطلبات')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-chart-bar text-primary"></i>
                            إحصائيات الطلبات
                        </h4>
                    </div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
            </div>
        </div>

        <!-- فلتر التاريخ -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> تصفية
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- البطاقات الإحصائية -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">إجمالي الطلبات</h6>
                                <h2 class="mb-0">{{ number_format($statistics['total'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-clipboard-list fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-secondary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">طلبات جديدة</h6>
                                <h2 class="mb-0">{{ number_format($statistics['new'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-plus-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">في التفاوض</h6>
                                <h2 class="mb-0">{{ number_format($statistics['in_negotiation'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-comments fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">معتمدة</h6>
                                <h2 class="mb-0">{{ number_format($statistics['approved'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">قيد التنفيذ</h6>
                                <h2 class="mb-0">{{ number_format($statistics['in_progress'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-truck fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">مكتملة</h6>
                                <h2 class="mb-0">{{ number_format($statistics['completed'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-flag-checkered fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">ملغاة</h6>
                                <h2 class="mb-0">{{ number_format($statistics['cancelled'] ?? 0) }}</h2>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- القيم المالية -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> القيمة الإجمالية للطلبات</h5>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="text-success mb-0">{{ number_format($statistics['total_value'] ?? 0, 2) }}</h2>
                        <p class="text-muted">د.ع</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-check-double"></i> القيمة المنفذة</h5>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="text-primary mb-0">{{ number_format($statistics['executed_value'] ?? 0, 2) }}</h2>
                        <p class="text-muted">د.ع</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- مخطط بياني -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> توزيع الطلبات حسب الحالة</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('ordersChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['جديدة', 'في التفاوض', 'معتمدة', 'قيد التنفيذ', 'مكتملة', 'ملغاة'],
                    datasets: [{
                        data: [
                            {{ $statistics['new'] ?? 0 }},
                            {{ $statistics['in_negotiation'] ?? 0 }},
                            {{ $statistics['approved'] ?? 0 }},
                            {{ $statistics['in_progress'] ?? 0 }},
                            {{ $statistics['completed'] ?? 0 }},
                            {{ $statistics['cancelled'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#6c757d',
                            '#ffc107',
                            '#28a745',
                            '#17a2b8',
                            '#198754',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush
