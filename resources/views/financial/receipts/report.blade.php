@extends('layouts.app')

@section('title', 'تقرير المقبوضات')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-chart-bar me-2"></i>تقرير المقبوضات</h4>
            <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع
            </a>
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
                        <label class="form-label">نوع الدافع</label>
                        <select name="payer_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\PaymentReceipt::PAYER_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ request('payer_type') == $key ? 'selected' : '' }}>
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
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date', $fromDate) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date', $toDate) }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- الملخص -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($report['total_amount'] ?? 0, 0) }}</h3>
                        <small>إجمالي المقبوضات</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $report['count'] ?? 0 }}</h3>
                        <small>عدد الإيصالات</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($report['average'] ?? 0, 0) }}</h3>
                        <small>متوسط الإيصال</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- حسب نوع الدافع -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">حسب نوع الدافع</div>
                    <div class="card-body">
                        @if (isset($report['by_payer_type']) && count($report['by_payer_type']) > 0)
                            @foreach ($report['by_payer_type'] as $type => $data)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ \App\Models\PaymentReceipt::PAYER_TYPES[$type] ?? $type }}</span>
                                    <span class="fw-bold text-success">{{ number_format($data['total'], 0) }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 5px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ ($data['total'] / max($report['total_amount'], 1)) * 100 }}%">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">لا توجد بيانات</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- حسب طريقة الدفع -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">حسب طريقة الدفع</div>
                    <div class="card-body">
                        @if (isset($report['by_payment_method']) && count($report['by_payment_method']) > 0)
                            @foreach ($report['by_payment_method'] as $method => $data)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ \App\Models\PaymentReceipt::PAYMENT_METHODS[$method] ?? $method }}</span>
                                    <span class="fw-bold text-primary">{{ number_format($data['total'], 0) }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 5px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ ($data['total'] / max($report['total_amount'], 1)) * 100 }}%">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">لا توجد بيانات</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
