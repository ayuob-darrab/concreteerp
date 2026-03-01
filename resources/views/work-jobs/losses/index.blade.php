@extends('layouts.app')

@section('title', 'قائمة الخسائر')

@section('content')
    <div class="container-fluid">
        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['total_count'] ?? 0 }}</h3>
                        <small>إجمالي الخسائر</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['open_count'] ?? 0 }}</h3>
                        <small>مفتوحة</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['resolved_count'] ?? 0 }}</h3>
                        <small>محلولة</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_quantity_lost'] ?? 0, 1) }}</h3>
                        <small>كمية مفقودة (م³)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_actual_cost'] ?? ($statistics['total_estimated_cost'] ?? 0), 0) }}
                        </h3>
                        <small>إجمالي التكلفة</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- العنوان والإجراءات -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>سجل الخسائر</h4>
            <div>
                <a href="{{ route('losses.statistics') }}" class="btn btn-secondary">
                    <i class="fas fa-chart-bar"></i> الإحصائيات
                </a>
                <a href="{{ route('losses.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus"></i> تسجيل خسارة
                </a>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">نوع الخسارة</label>
                        <select name="loss_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\WorkLoss::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ request('loss_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\WorkLoss::STATUSES as $key => $label)
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
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('losses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول الخسائر -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>أمر العمل</th>
                                <th>الوصف</th>
                                <th>الكمية</th>
                                <th>التكلفة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($losses as $loss)
                                <tr>
                                    <td>{{ $loss->id }}</td>
                                    <td>{{ $loss->reported_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <i class="fas {{ $loss->type_icon }} text-danger me-1"></i>
                                        {{ $loss->type_label }}
                                    </td>
                                    <td>
                                        @if ($loss->job)
                                            <a
                                                href="{{ route('work-jobs.show', $loss->job) }}">{{ $loss->job->job_number }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($loss->description, 50) }}</td>
                                    <td>{{ $loss->quantity_lost ? number_format($loss->quantity_lost, 1) . ' م³' : '-' }}
                                    </td>
                                    <td>{{ number_format($loss->total_cost, 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $loss->status_badge }}">{{ $loss->status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('losses.show', $loss) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <p class="text-muted">لا توجد خسائر مسجلة</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $losses->links() }}
            </div>
        </div>
    </div>
@endsection
