@extends('layouts.app')

@section('title', 'تقرير الفترة')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-chart-line me-2"></i>تقرير الصندوق للفترة</h4>
            <a href="{{ route('cash.daily') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> الصندوق اليومي
            </a>
        </div>

        <!-- فلاتر الفترة -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الفرع</label>
                        <select name="branch_id" class="form-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date', $fromDate) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate) }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> عرض
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ملخص الفترة -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-light h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-day fa-2x text-secondary mb-2"></i>
                        <small class="text-muted d-block">عدد الأيام</small>
                        <h3>{{ $periodSummary['days_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-down fa-2x text-success mb-2"></i>
                        <small class="text-muted d-block">إجمالي المقبوضات</small>
                        <h3 class="text-success">{{ number_format($periodSummary['total_receipts'] ?? 0, 0) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger bg-opacity-10 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-up fa-2x text-danger mb-2"></i>
                        <small class="text-muted d-block">إجمالي المدفوعات</small>
                        <h3 class="text-danger">{{ number_format($periodSummary['total_payments'] ?? 0, 0) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary bg-opacity-10 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                        <small class="text-muted d-block">صافي الفترة</small>
                        @php $net = ($periodSummary['total_receipts'] ?? 0) - ($periodSummary['total_payments'] ?? 0); @endphp
                        <h3 class="{{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($net, 0) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الأيام -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table me-2"></i>تفاصيل الأيام
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th class="text-end">الرصيد الافتتاحي</th>
                                <th class="text-center">المقبوضات</th>
                                <th class="text-center">المدفوعات</th>
                                <th class="text-end">الرصيد الختامي</th>
                                <th class="text-center">الحالة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySummaries as $day)
                                <tr>
                                    <td>
                                        <strong>{{ $day->summary_date->format('Y-m-d') }}</strong>
                                        <br><small
                                            class="text-muted">{{ $day->summary_date->locale('ar')->format('l') }}</small>
                                    </td>
                                    <td class="text-end">{{ number_format($day->opening_balance, 0) }}</td>
                                    <td class="text-center">
                                        <span class="text-success">+{{ number_format($day->total_receipts, 0) }}</span>
                                        <br><small class="text-muted">({{ $day->receipts_count }})</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-danger">-{{ number_format($day->total_payments, 0) }}</span>
                                        <br><small class="text-muted">({{ $day->payments_count }})</small>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($day->closing_balance, 0) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $day->is_open ? 'success' : 'secondary' }}">
                                            {{ $day->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('cash.daily', ['date' => $day->summary_date->format('Y-m-d')]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        لا توجد بيانات لهذه الفترة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($dailySummaries->count() > 0)
                            <tfoot class="table-primary">
                                <tr>
                                    <th>الإجمالي</th>
                                    <th class="text-end">
                                        {{ number_format($dailySummaries->first()->opening_balance ?? 0, 0) }}</th>
                                    <th class="text-center text-success">
                                        {{ number_format($dailySummaries->sum('total_receipts'), 0) }}</th>
                                    <th class="text-center text-danger">
                                        {{ number_format($dailySummaries->sum('total_payments'), 0) }}</th>
                                    <th class="text-end">
                                        {{ number_format($dailySummaries->last()->closing_balance ?? 0, 0) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
