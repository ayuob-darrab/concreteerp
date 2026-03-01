@extends('layouts.app')

@section('title', 'سندات الصرف')

@section('content')
    <div class="container-fluid">
        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3>{{ $statistics['pending_approval'] ?? 0 }}</h3>
                        <small>بانتظار الموافقة</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($statistics['total_today'] ?? 0, 0) }}</h3>
                        <small>مصروفات اليوم</small>
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

        <!-- العنوان -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-file-invoice-dollar me-2"></i>سندات الصرف</h4>
            <div>
                @if ($statistics['pending_approval'] > 0)
                    <a href="{{ route('vouchers.pending-approval') }}" class="btn btn-warning">
                        <i class="fas fa-clock"></i> الموافقات ({{ $statistics['pending_approval'] }})
                    </a>
                @endif
                <a href="{{ route('vouchers.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus"></i> سند جديد
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
                            @foreach (\App\Models\PaymentVoucher::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع المستفيد</label>
                        <select name="payee_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\PaymentVoucher::PAYEE_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ request('payee_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
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
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i></button>
                        <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-undo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- الجدول -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>رقم السند</th>
                                <th>التاريخ</th>
                                <th>المستفيد</th>
                                <th>المبلغ</th>
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('vouchers.show', $voucher) }}">{{ $voucher->voucher_number }}</a>
                                    </td>
                                    <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $voucher->payee_name }}
                                        <br><small class="text-muted">{{ $voucher->payee_type_label }}</small>
                                    </td>
                                    <td class="text-danger fw-bold">{{ $voucher->formatted_amount }}</td>
                                    <td>{{ $voucher->payment_method_label }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $voucher->status_badge }}">{{ $voucher->status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vouchers.show', $voucher) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($voucher->status === 'paid')
                                            <a href="{{ route('vouchers.print', $voucher) }}"
                                                class="btn btn-sm btn-outline-secondary" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد سندات صرف</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
@endsection
