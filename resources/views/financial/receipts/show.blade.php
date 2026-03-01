@extends('layouts.app')

@section('title', 'إيصال قبض #' . $receipt->receipt_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            إيصال قبض #{{ $receipt->receipt_number }}
                        </h5>
                        <span class="badge bg-{{ $receipt->status_badge }} fs-6">{{ $receipt->status_label }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="text-muted" style="width: 40%">الدافع:</th>
                                        <td class="fw-bold">{{ $receipt->payer_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الصفة:</th>
                                        <td>{{ $receipt->payer_type_label }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الهاتف:</th>
                                        <td>{{ $receipt->payer_phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الفرع:</th>
                                        <td>{{ $receipt->branch->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="text-muted" style="width: 40%">تاريخ القبض:</th>
                                        <td>{{ $receipt->received_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">طريقة الدفع:</th>
                                        <td>{{ $receipt->payment_method_label }}</td>
                                    </tr>
                                    @if ($receipt->reference_number)
                                        <tr>
                                            <th class="text-muted">رقم المرجع:</th>
                                            <td>{{ $receipt->reference_number }}</td>
                                        </tr>
                                    @endif
                                    @if ($receipt->check_number)
                                        <tr>
                                            <th class="text-muted">رقم الشيك:</th>
                                            <td>{{ $receipt->check_number }} - {{ $receipt->bank_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">تاريخ الشيك:</th>
                                            <td>{{ $receipt->check_date ? $receipt->check_date->format('Y-m-d') : '-' }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr>

                        <!-- المبلغ -->
                        <div class="bg-success bg-opacity-10 p-4 rounded text-center mb-4">
                            <small class="text-muted d-block">المبلغ المستلم</small>
                            <h2 class="text-success mb-1">{{ $receipt->formatted_amount }}</h2>
                            @if ($receipt->amount_in_words)
                                <small class="text-muted">{{ $receipt->amount_in_words }}</small>
                            @endif
                            @if ($receipt->currency_code !== 'IQD')
                                <div class="mt-2">
                                    <small class="text-info">≈ {{ number_format($receipt->amount_in_default, 0) }}
                                        د.ع</small>
                                </div>
                            @endif
                        </div>

                        <!-- الوصف -->
                        <div class="mb-4">
                            <h6 class="text-muted">سبب الدفع:</h6>
                            <p class="lead">{{ $receipt->description }}</p>
                        </div>

                        @if ($receipt->notes)
                            <div class="mb-4">
                                <h6 class="text-muted">ملاحظات:</h6>
                                <p>{{ $receipt->notes }}</p>
                            </div>
                        @endif

                        <!-- حالة الشيك المرتجع -->
                        @if ($receipt->status === 'bounced')
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>شيك مرتجع!</h6>
                                <p class="mb-0">تم إرجاع هذا الشيك من البنك.</p>
                            </div>
                        @endif

                        <!-- حالة الإلغاء -->
                        @if ($receipt->status === 'cancelled')
                            <div class="alert alert-secondary">
                                <h6 class="alert-heading"><i class="fas fa-ban me-2"></i>ملغي</h6>
                                <p class="mb-0">
                                    @if ($receipt->cancelled_at)
                                        تم الإلغاء: {{ $receipt->cancelled_at->format('Y-m-d H:i') }}
                                    @endif
                                    @if ($receipt->canceller)
                                        بواسطة: {{ $receipt->canceller->name }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cogs me-2"></i>الإجراءات
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('receipts.print', $receipt) }}" class="btn btn-outline-secondary"
                                target="_blank">
                                <i class="fas fa-print me-2"></i>طباعة الإيصال
                            </a>

                            @if ($receipt->canCancel())
                                <form action="{{ route('receipts.cancel', $receipt) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('إلغاء الإيصال؟ سيتم تعديل أرصدة الحسابات.')">
                                        <i class="fas fa-ban me-2"></i>إلغاء الإيصال
                                    </button>
                                </form>
                            @endif

                            @if ($receipt->payment_method === 'check' && $receipt->status === 'confirmed')
                                <form action="{{ route('receipts.mark-bounced', $receipt) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('تسجيل الشيك كمرتجع؟')">
                                        <i class="fas fa-exclamation-triangle me-2"></i>شيك مرتجع
                                    </button>
                                </form>
                            @endif

                            <hr>

                            <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="card mt-3">
                    <div class="card-header"><i class="fas fa-info-circle me-2"></i>معلومات</div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>استلمه:</strong> {{ $receipt->receiver->name ?? '-' }}<br>
                            <strong>تاريخ الإنشاء:</strong> {{ $receipt->created_at->format('Y-m-d H:i') }}<br>
                            <strong>آخر تحديث:</strong> {{ $receipt->updated_at->format('Y-m-d H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
