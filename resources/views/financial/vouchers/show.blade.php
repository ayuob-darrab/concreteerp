@extends('layouts.app')

@section('title', 'سند صرف #' . $voucher->voucher_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            سند صرف #{{ $voucher->voucher_number }}
                        </h5>
                        <span class="badge bg-{{ $voucher->status_badge }} fs-6">{{ $voucher->status_label }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="text-muted" style="width: 40%">المستفيد:</th>
                                        <td class="fw-bold">{{ $voucher->payee_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الصفة:</th>
                                        <td>{{ $voucher->payee_type_label }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الهاتف:</th>
                                        <td>{{ $voucher->payee_phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">الفرع:</th>
                                        <td>{{ $voucher->branch->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="text-muted" style="width: 40%">تاريخ الإنشاء:</th>
                                        <td>{{ $voucher->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">طريقة الدفع:</th>
                                        <td>{{ $voucher->payment_method_label }}</td>
                                    </tr>
                                    @if ($voucher->reference_number)
                                        <tr>
                                            <th class="text-muted">رقم المرجع:</th>
                                            <td>{{ $voucher->reference_number }}</td>
                                        </tr>
                                    @endif
                                    @if ($voucher->check_number)
                                        <tr>
                                            <th class="text-muted">رقم الشيك:</th>
                                            <td>{{ $voucher->check_number }} - {{ $voucher->bank_name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr>

                        <!-- المبلغ -->
                        <div class="bg-light p-4 rounded text-center mb-4">
                            <small class="text-muted d-block">المبلغ</small>
                            <h2 class="text-danger mb-1">{{ $voucher->formatted_amount }}</h2>
                            @if ($voucher->amount_in_words)
                                <small class="text-muted">{{ $voucher->amount_in_words }}</small>
                            @endif
                            @if ($voucher->currency_code !== 'IQD')
                                <div class="mt-2">
                                    <small class="text-info">≈ {{ number_format($voucher->amount_in_default, 0) }}
                                        د.ع</small>
                                </div>
                            @endif
                        </div>

                        <!-- الوصف -->
                        <div class="mb-4">
                            <h6 class="text-muted">سبب الصرف:</h6>
                            <p class="lead">{{ $voucher->description }}</p>
                        </div>

                        <!-- معلومات الموافقة -->
                        @if ($voucher->requires_approval)
                            <div
                                class="alert alert-{{ $voucher->status === 'approved' || $voucher->status === 'paid' ? 'success' : ($voucher->status === 'rejected' ? 'danger' : 'warning') }}">
                                <h6 class="alert-heading">
                                    <i class="fas fa-{{ $voucher->approver ? 'check-circle' : 'clock' }} me-2"></i>
                                    حالة الموافقة
                                </h6>
                                @if ($voucher->approver)
                                    <p class="mb-0">
                                        {{ $voucher->status === 'rejected' ? 'رُفض' : 'تمت الموافقة' }} بواسطة:
                                        <strong>{{ $voucher->approver->name }}</strong>
                                        <br><small>{{ $voucher->approved_at->format('Y-m-d H:i') }}</small>
                                    </p>
                                    @if ($voucher->rejection_reason)
                                        <hr>
                                        <p class="mb-0"><strong>سبب الرفض:</strong> {{ $voucher->rejection_reason }}</p>
                                    @endif
                                @else
                                    <p class="mb-0">بانتظار الموافقة</p>
                                @endif
                            </div>
                        @endif

                        <!-- معلومات الصرف -->
                        @if ($voucher->paid_at)
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-money-bill-wave me-2"></i>تم الصرف</h6>
                                <p class="mb-0">
                                    بواسطة: <strong>{{ $voucher->payer->name ?? 'غير معروف' }}</strong>
                                    <br>التاريخ: {{ $voucher->paid_at->format('Y-m-d H:i') }}
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
                            <!-- طباعة -->
                            @if ($voucher->status === 'paid')
                                <a href="{{ route('vouchers.print', $voucher) }}" class="btn btn-outline-secondary"
                                    target="_blank">
                                    <i class="fas fa-print me-2"></i>طباعة السند
                                </a>
                            @endif

                            <!-- تقديم للموافقة -->
                            @if ($voucher->status === 'draft')
                                <form action="{{ route('vouchers.submit-for-approval', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-paper-plane me-2"></i>تقديم للموافقة
                                    </button>
                                </form>
                            @endif

                            <!-- الموافقة / الرفض -->
                            @if ($voucher->status === 'pending_approval' && auth()->user()->can('approve', $voucher))
                                <form action="{{ route('vouchers.approve', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('موافقة على السند؟')">
                                        <i class="fas fa-check me-2"></i>موافقة
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times me-2"></i>رفض
                                </button>
                            @endif

                            <!-- الصرف -->
                            @if ($voucher->canPay())
                                <form action="{{ route('vouchers.pay', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100"
                                        onclick="return confirm('تأكيد الصرف؟')">
                                        <i class="fas fa-money-bill-wave me-2"></i>تنفيذ الصرف
                                    </button>
                                </form>
                            @endif

                            <!-- الإلغاء -->
                            @if ($voucher->canCancel())
                                <form action="{{ route('vouchers.cancel', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('إلغاء السند؟')">
                                        <i class="fas fa-ban me-2"></i>إلغاء
                                    </button>
                                </form>
                            @endif

                            <hr>

                            <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
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
                            <strong>أنشأه:</strong> {{ $voucher->creator->name ?? '-' }}<br>
                            <strong>تاريخ الإنشاء:</strong> {{ $voucher->created_at->format('Y-m-d H:i') }}<br>
                            <strong>آخر تحديث:</strong> {{ $voucher->updated_at->format('Y-m-d H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الرفض -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('vouchers.reject', $voucher) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">رفض السند</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
