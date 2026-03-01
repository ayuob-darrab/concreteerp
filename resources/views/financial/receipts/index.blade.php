@extends('layouts.app')

@section('title', 'إيصالات القبض')

@section('content')
    <div class="container-fluid">
        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_today'] ?? 0, 0) }}</h3>
                        <small>إيصالات اليوم</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['count_today'] ?? 0 }}</h3>
                        <small>عدد الإيصالات اليوم</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_month'] ?? 0, 0) }}</h3>
                        <small>إجمالي الشهر</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- العنوان والإجراءات -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-receipt me-2"></i>إيصالات القبض</h4>
            <div>
                <a href="{{ route('receipts.report') }}" class="btn btn-secondary">
                    <i class="fas fa-chart-bar"></i> تقرير
                </a>
                <a href="{{ route('receipts.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> إيصال جديد
                </a>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">الفرع</label>
                        <select name="branch_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\PaymentReceipt::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\PaymentReceipt::PAYMENT_METHODS as $key => $label)
                                <option value="{{ $key }}"
                                    {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول الإيصالات -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الإيصال</th>
                                <th>التاريخ</th>
                                <th>الدافع</th>
                                <th>المبلغ</th>
                                <th>طريقة الدفع</th>
                                <th>الفرع</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('receipts.show', $receipt) }}">{{ $receipt->receipt_number }}</a>
                                    </td>
                                    <td>{{ $receipt->received_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        {{ $receipt->payer_name }}
                                        <br><small class="text-muted">{{ $receipt->payer_type_label }}</small>
                                    </td>
                                    <td class="text-success fw-bold">{{ $receipt->formatted_amount }}</td>
                                    <td>{{ $receipt->payment_method_label }}</td>
                                    <td>{{ $receipt->branch->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $receipt->status_badge }}">{{ $receipt->status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('receipts.show', $receipt) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('receipts.print', $receipt) }}"
                                            class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد إيصالات</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $receipts->links() }}
            </div>
        </div>
    </div>
@endsection
