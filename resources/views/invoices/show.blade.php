@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة: ' . $invoice->invoice_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- معلومات الفاتورة -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-file-invoice me-2"></i>
                                فاتورة رقم: {{ $invoice->invoice_number }}
                            </h4>
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
                            <span class="badge {{ $statusClasses[$invoice->status] ?? 'bg-secondary' }} fs-6">
                                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- معلومات المقاول والتاريخ -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">معلومات المقاول</h6>
                                <p class="mb-1">
                                    <strong>الاسم:</strong>
                                    <a href="{{ route('contractors.show', $invoice->contractor_id) }}">
                                        {{ $invoice->contractor?->name }}
                                    </a>
                                </p>
                                <p class="mb-1"><strong>الهاتف:</strong> {{ $invoice->contractor?->phone }}</p>
                                <p class="mb-0"><strong>العنوان:</strong> {{ $invoice->contractor?->address ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">معلومات الفاتورة</h6>
                                <p class="mb-1"><strong>تاريخ الفاتورة:</strong>
                                    {{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                                <p class="mb-1"><strong>تاريخ الاستحقاق:</strong>
                                    {{ $invoice->due_date?->format('Y-m-d') }}</p>
                                @if ($invoice->work_order_id)
                                    <p class="mb-0">
                                        <strong>أمر العمل:</strong>
                                        <a href="{{ route('work-orders.show', $invoice->work_order_id) }}">
                                            {{ $invoice->workOrder?->order_number }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- بنود الفاتورة -->
                        <h6 class="text-muted mb-3">بنود الفاتورة</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الوصف</th>
                                        <th>الكمية</th>
                                        <th>الوحدة</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ number_format($item->quantity, 2) }}</td>
                                            <td>{{ $item->unit }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-start">المجموع الفرعي</th>
                                        <th>{{ number_format($invoice->subtotal, 2) }}</th>
                                    </tr>
                                    @if ($invoice->discount_amount > 0)
                                        <tr>
                                            <th colspan="5" class="text-start">
                                                الخصم
                                                @if ($invoice->discount_type === 'percentage')
                                                    ({{ $invoice->discount_value }}%)
                                                @endif
                                            </th>
                                            <th class="text-danger">- {{ number_format($invoice->discount_amount, 2) }}
                                            </th>
                                        </tr>
                                    @endif
                                    @if ($invoice->tax_amount > 0)
                                        <tr>
                                            <th colspan="5" class="text-start">
                                                الضريبة ({{ $invoice->tax_rate }}%)
                                            </th>
                                            <th>{{ number_format($invoice->tax_amount, 2) }}</th>
                                        </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="5" class="text-start">الإجمالي</th>
                                        <th>{{ number_format($invoice->total_amount, 2) }} د.ع</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th colspan="5" class="text-start">المدفوع</th>
                                        <th>{{ number_format($invoice->paid_amount, 2) }} د.ع</th>
                                    </tr>
                                    <tr class="table-warning">
                                        <th colspan="5" class="text-start">المتبقي</th>
                                        <th>{{ number_format($invoice->remaining_amount, 2) }} د.ع</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- ملاحظات -->
                        @if ($invoice->notes)
                            <div class="mt-4">
                                <h6 class="text-muted">ملاحظات</h6>
                                <p class="mb-0">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الإجراءات والمعلومات الإضافية -->
            <div class="col-md-4">
                <!-- إجراءات -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            الإجراءات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($invoice->status === 'draft')
                                <a href="{{ route('contractor-invoices.edit', $invoice) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-edit me-2"></i>
                                    تعديل الفاتورة
                                </a>
                                <form action="{{ route('contractor-invoices.issue', $invoice) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>
                                        إصدار الفاتورة
                                    </button>
                                </form>
                            @endif

                            @if (in_array($invoice->status, ['issued', 'partially_paid', 'overdue']))
                                <a href="{{ route('contractor-receipts.create-receipt') }}?invoice_id={{ $invoice->id }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-money-bill me-2"></i>
                                    تسجيل دفعة
                                </a>
                            @endif

                            <a href="{{ route('contractor-invoices.print', $invoice) }}" class="btn btn-outline-info"
                                target="_blank">
                                <i class="fas fa-print me-2"></i>
                                طباعة
                            </a>

                            <a href="{{ route('contractor-invoices.download', $invoice) }}" class="btn btn-outline-dark">
                                <i class="fas fa-download me-2"></i>
                                تحميل PDF
                            </a>

                            @if (in_array($invoice->status, ['draft', 'issued']))
                                <form action="{{ route('contractor-invoices.cancel', $invoice) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times me-2"></i>
                                        إلغاء الفاتورة
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- سجل الدفعات -->
                @if ($invoice->payments && $invoice->payments->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>
                                سجل الدفعات
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($invoice->payments as $payment)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ number_format($payment->amount, 2) }} د.ع</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $payment->created_at?->format('Y-m-d H:i') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $payment->payment_method === 'cash' ? 'success' : 'info' }}">
                                                {{ $payment->payment_method === 'cash' ? 'نقدي' : 'شيك' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- معلومات إضافية -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات إضافية
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>أنشئت بواسطة:</strong>
                            {{ $invoice->createdBy?->name ?? '-' }}
                        </p>
                        <p class="mb-2">
                            <strong>تاريخ الإنشاء:</strong>
                            {{ $invoice->created_at?->format('Y-m-d H:i') }}
                        </p>
                        @if ($invoice->issued_at)
                            <p class="mb-2">
                                <strong>تاريخ الإصدار:</strong>
                                {{ $invoice->issued_at?->format('Y-m-d H:i') }}
                            </p>
                        @endif
                        @if ($invoice->cancelled_at)
                            <p class="mb-2 text-danger">
                                <strong>تاريخ الإلغاء:</strong>
                                {{ $invoice->cancelled_at?->format('Y-m-d H:i') }}
                            </p>
                            @if ($invoice->cancellation_reason)
                                <p class="mb-0 text-danger">
                                    <strong>سبب الإلغاء:</strong>
                                    {{ $invoice->cancellation_reason }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
