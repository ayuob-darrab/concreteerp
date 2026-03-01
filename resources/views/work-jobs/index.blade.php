@extends('layouts.app')

@section('title', 'أوامر العمل')

@section('content')
    <div class="container-fluid">
        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['total'] ?? 0 }}</h3>
                        <small>إجمالي الأوامر</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['pending'] ?? 0 }}</h3>
                        <small>بانتظار التنفيذ</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['in_progress'] ?? 0 }}</h3>
                        <small>قيد التنفيذ</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['completed'] ?? 0 }}</h3>
                        <small>مكتملة</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['executed_quantity'] ?? 0, 1) }}</h3>
                        <small>الكمية المنفذة م³</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-dark text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_revenue'] ?? 0, 0) }}</h3>
                        <small>الإيرادات</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- العنوان والإجراءات -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-hard-hat me-2"></i>أوامر العمل</h4>
            <div>
                <a href="{{ route('work-jobs.daily') }}" class="btn btn-info">
                    <i class="fas fa-calendar-day"></i> أوامر اليوم
                </a>
                <a href="{{ route('work-jobs.statistics') }}" class="btn btn-secondary">
                    <i class="fas fa-chart-bar"></i> الإحصائيات
                </a>
                <a href="{{ route('work-jobs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> أمر عمل جديد
                </a>
            </div>
        </div>

        <!-- فلاتر البحث -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\WorkJob::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="رقم الأمر، العميل، العنوان..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('work-jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول أوامر العمل -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الأمر</th>
                                <th>تاريخ التنفيذ</th>
                                <th>العميل</th>
                                <th>نوع الخرسانة</th>
                                <th>الكمية</th>
                                <th>التقدم</th>
                                <th>الحالة</th>
                                <th>الشحنات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td>
                                        <a href="{{ route('work-jobs.show', $job) }}" class="fw-bold text-decoration-none">
                                            {{ $job->job_number }}
                                        </a>
                                        @if ($job->order)
                                            <br><small class="text-muted">طلب: {{ $job->order->order_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $job->scheduled_date->format('Y-m-d') }}
                                        @if ($job->scheduled_time)
                                            <br><small>{{ $job->scheduled_time->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $job->customer_name ?? 'غير محدد' }}
                                        @if ($job->customer_phone)
                                            <br><small class="text-muted">{{ $job->customer_phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $job->concreteType->name ?? '-' }}</td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($job->total_quantity, 1) }}</span> م³
                                        <br>
                                        <small class="text-success">منفذ:
                                            {{ number_format($job->executed_quantity, 1) }}</small>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $job->completion_percentage }}%">
                                                {{ number_format($job->completion_percentage, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $job->status_badge }}">
                                            {{ $job->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $job->total_shipments }} شحنة</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('work-jobs.show', $job) }}"
                                                class="btn btn-sm btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('work-jobs.print', $job) }}"
                                                class="btn btn-sm btn-outline-secondary" title="طباعة" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد أوامر عمل</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $jobs->links() }}
            </div>
        </div>
    </div>
@endsection
