@extends('layouts.app')

@section('title', 'السندات')

@section('content')
    <div class="container-fluid">
        <!-- إحصائيات -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">سندات القبض</h6>
                                <h3 class="mb-0">{{ number_format($statistics['total_receipts'] ?? 0, 2) }}</h3>
                                <small>{{ $statistics['receipts_count'] ?? 0 }} سند</small>
                            </div>
                            <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">سندات الصرف</h6>
                                <h3 class="mb-0">{{ number_format($statistics['total_payments'] ?? 0, 2) }}</h3>
                                <small>{{ $statistics['payments_count'] ?? 0 }} سند</small>
                            </div>
                            <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">بانتظار الموافقة</h6>
                                <h3 class="mb-0">{{ $statistics['pending_count'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- السندات -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        قائمة السندات
                    </h4>
                    <div>
                        <a href="{{ route('contractor-receipts.create-receipt') }}" class="btn btn-success me-2">
                            <i class="fas fa-plus me-2"></i>
                            سند قبض
                        </a>
                        <a href="{{ route('contractor-receipts.create-payment') }}" class="btn btn-danger">
                            <i class="fas fa-plus me-2"></i>
                            سند صرف
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- فلترة -->
                <form method="GET" action="{{ route('contractor-receipts.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث برقم السند..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>سند قبض
                                </option>
                                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>سند صرف
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from_date" class="form-control" placeholder="من تاريخ"
                                value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to_date" class="form-control" placeholder="إلى تاريخ"
                                value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- جدول السندات -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم السند</th>
                                <th>النوع</th>
                                <th>التاريخ</th>
                                <th>المقاول</th>
                                <th>المبلغ</th>
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts ?? [] as $receipt)
                                <tr>
                                    <td>
                                        <a href="{{ route('contractor-receipts.show', $receipt) }}">
                                            {{ $receipt->receipt_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($receipt->type === 'receipt')
                                            <span class="badge bg-success">
                                                <i class="fas fa-arrow-down me-1"></i>
                                                قبض
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-arrow-up me-1"></i>
                                                صرف
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $receipt->receipt_date?->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('contractors.show', $receipt->contractor_id) }}">
                                            {{ $receipt->contractor?->name }}
                                        </a>
                                    </td>
                                    <td class="{{ $receipt->type === 'receipt' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($receipt->amount, 2) }} د.ع
                                    </td>
                                    <td>
                                        @php
                                            $paymentMethods = [
                                                'cash' => 'نقدي',
                                                'check' => 'شيك',
                                                'transfer' => 'تحويل',
                                            ];
                                        @endphp
                                        {{ $paymentMethods[$receipt->payment_method] ?? $receipt->payment_method }}
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'معلق',
                                                'approved' => 'معتمد',
                                                'cancelled' => 'ملغي',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$receipt->status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$receipt->status] ?? $receipt->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('contractor-receipts.show', $receipt) }}"
                                                class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($receipt->status === 'pending')
                                                <form action="{{ route('contractor-receipts.approve', $receipt) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="اعتماد">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('contractor-receipts.cancel', $receipt) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('هل أنت متأكد من إلغاء هذا السند؟');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger" title="إلغاء">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('contractor-receipts.print', $receipt) }}"
                                                class="btn btn-outline-info" title="طباعة" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">لا توجد سندات</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- التصفح -->
                @if (isset($receipts) && $receipts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $receipts->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
