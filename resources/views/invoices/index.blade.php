@extends('layouts.app')

@section('title', 'الفواتير')

@section('content')
    <div class="container-fluid">
        <!-- إحصائيات -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">إجمالي الفواتير</h6>
                                <h3 class="mb-0">{{ number_format($statistics['total_amount'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">المحصل</h6>
                                <h3 class="mb-0">{{ number_format($statistics['paid_amount'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">المتبقي</h6>
                                <h3 class="mb-0">{{ number_format($statistics['remaining_amount'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">المتأخرة</h6>
                                <h3 class="mb-0">{{ $statistics['overdue_count'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الفواتير -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        قائمة الفواتير
                    </h4>
                    <a href="{{ route('contractor-invoices.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        فاتورة جديدة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- فلترة -->
                <form method="GET" action="{{ route('contractor-invoices.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث برقم الفاتورة أو اسم المقاول..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>صادرة</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                                <option value="partially_paid"
                                    {{ request('status') == 'partially_paid' ? 'selected' : '' }}>مدفوعة جزئياً</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخرة
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="contractor_id" class="form-select">
                                <option value="">كل المقاولين</option>
                                @foreach ($contractors ?? [] as $contractor)
                                    <option value="{{ $contractor->id }}"
                                        {{ request('contractor_id') == $contractor->id ? 'selected' : '' }}>
                                        {{ $contractor->name }}
                                    </option>
                                @endforeach
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

                <!-- جدول الفواتير -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>المقاول</th>
                                <th>الإجمالي</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>الاستحقاق</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices ?? [] as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('contractor-invoices.show', $invoice) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('contractors.show', $invoice->contractor_id) }}">
                                            {{ $invoice->contractor?->name }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="text-success">{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td class="text-danger">{{ number_format($invoice->remaining_amount, 2) }}</td>
                                    <td>{{ $invoice->due_date?->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-secondary',
                                                'issued' => 'bg-primary',
                                                'paid' => 'bg-success',
                                                'partially_paid' => 'bg-info',
                                                'overdue' => 'bg-danger',
                                                'cancelled' => 'bg-dark',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'مسودة',
                                                'issued' => 'صادرة',
                                                'paid' => 'مدفوعة',
                                                'partially_paid' => 'مدفوعة جزئياً',
                                                'overdue' => 'متأخرة',
                                                'cancelled' => 'ملغاة',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$invoice->status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('contractor-invoices.show', $invoice) }}"
                                                class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($invoice->status === 'draft')
                                                <a href="{{ route('contractor-invoices.edit', $invoice) }}"
                                                    class="btn btn-outline-secondary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('contractor-invoices.issue', $invoice) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success"
                                                        title="إصدار">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('contractor-invoices.print', $invoice) }}"
                                                class="btn btn-outline-info" title="طباعة" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('contractor-invoices.download', $invoice) }}"
                                                class="btn btn-outline-dark" title="تحميل PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">لا توجد فواتير</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- التصفح -->
                @if (isset($invoices) && $invoices->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $invoices->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
