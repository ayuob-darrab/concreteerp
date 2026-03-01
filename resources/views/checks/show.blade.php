@extends('layouts.app')

@section('title', 'تفاصيل الشيك: ' . $check->check_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- معلومات الشيك -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-money-check me-2"></i>
                                شيك رقم: {{ $check->check_number }}
                            </h4>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'deposited' => 'bg-info',
                                    'collected' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'returned' => 'bg-secondary',
                                    'cancelled' => 'bg-dark',
                                    'endorsed' => 'bg-primary',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد الانتظار',
                                    'deposited' => 'مودع',
                                    'collected' => 'محصل',
                                    'rejected' => 'مرفوض',
                                    'returned' => 'مرتجع',
                                    'cancelled' => 'ملغي',
                                    'endorsed' => 'مظهر',
                                ];
                                $typeLabels = [
                                    'incoming' => 'وارد',
                                    'outgoing' => 'صادر',
                                ];
                            @endphp
                            <div>
                                <span class="badge bg-{{ $check->type === 'incoming' ? 'success' : 'info' }} me-2">
                                    {{ $typeLabels[$check->type] ?? $check->type }}
                                </span>
                                <span class="badge {{ $statusClasses[$check->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$check->status] ?? $check->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">معلومات الشيك</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 40%">رقم الشيك:</td>
                                        <td><strong>{{ $check->check_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">المبلغ:</td>
                                        <td><strong class="text-primary">{{ number_format($check->amount, 2) }} د.ع</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">البنك:</td>
                                        <td>{{ $check->bank_name }}</td>
                                    </tr>
                                    @if ($check->bank_branch)
                                        <tr>
                                            <td class="text-muted">الفرع:</td>
                                            <td>{{ $check->bank_branch }}</td>
                                        </tr>
                                    @endif
                                    @if ($check->account_number)
                                        <tr>
                                            <td class="text-muted">رقم الحساب:</td>
                                            <td>{{ $check->account_number }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">التواريخ والأطراف</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 40%">تاريخ الإصدار:</td>
                                        <td>{{ $check->issue_date?->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">تاريخ الاستحقاق:</td>
                                        <td>
                                            {{ $check->due_date?->format('Y-m-d') }}
                                            @if ($check->is_overdue)
                                                <span class="badge bg-danger ms-2">متأخر</span>
                                            @elseif($check->is_due_today)
                                                <span class="badge bg-warning ms-2">اليوم</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">المقاول:</td>
                                        <td>
                                            <a href="{{ route('contractors.show', $check->contractor_id) }}">
                                                {{ $check->contractor?->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    @if ($check->drawer_name)
                                        <tr>
                                            <td class="text-muted">الساحب:</td>
                                            <td>{{ $check->drawer_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($check->invoice)
                                        <tr>
                                            <td class="text-muted">الفاتورة:</td>
                                            <td>
                                                <a href="{{ route('contractor-invoices.show', $check->invoice_id) }}">
                                                    {{ $check->invoice->invoice_number }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if ($check->notes)
                            <div class="mt-3">
                                <h6 class="text-muted">ملاحظات</h6>
                                <p class="mb-0">{{ $check->notes }}</p>
                            </div>
                        @endif

                        @if ($check->check_image)
                            <div class="mt-4">
                                <h6 class="text-muted">صورة الشيك</h6>
                                <img src="{{ asset('storage/' . $check->check_image) }}" alt="صورة الشيك"
                                    class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- سجل التغييرات -->
                @if ($check->statusLogs && $check->statusLogs->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>
                                سجل التغييرات
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>من</th>
                                            <th>إلى</th>
                                            <th>بواسطة</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($check->statusLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $statusClasses[$log->from_status] ?? 'bg-secondary' }}">
                                                        {{ $statusLabels[$log->from_status] ?? ($log->from_status ?? '-') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $statusClasses[$log->to_status] ?? 'bg-secondary' }}">
                                                        {{ $statusLabels[$log->to_status] ?? $log->to_status }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->changedBy?->name ?? '-' }}</td>
                                                <td>{{ $log->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- الإجراءات -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            الإجراءات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($check->status === 'pending')
                                <form action="{{ route('contractor-checks.deposit', $check) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fas fa-university me-2"></i>
                                        إيداع في البنك
                                    </button>
                                </form>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#collectModal">
                                    <i class="fas fa-check-circle me-2"></i>
                                    تحصيل مباشر
                                </button>

                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#endorseModal">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    تظهير الشيك
                                </button>
                            @endif

                            @if ($check->status === 'deposited')
                                <form action="{{ route('contractor-checks.collect', $check) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle me-2"></i>
                                        تأكيد التحصيل
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times-circle me-2"></i>
                                    رفض الشيك
                                </button>
                            @endif

                            @if (in_array($check->status, ['pending', 'deposited']))
                                <form action="{{ route('contractor-checks.cancel', $check) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الشيك؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-ban me-2"></i>
                                        إلغاء الشيك
                                    </button>
                                </form>
                            @endif

                            @if ($check->status === 'rejected')
                                <form action="{{ route('contractor-checks.return', $check) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-undo me-2"></i>
                                        إرجاع للمقاول
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('contractor-checks.edit', $check) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit me-2"></i>
                                تعديل
                            </a>

                            <a href="{{ route('contractor-checks.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- معلومات التظهير -->
                @if ($check->is_endorsed && $check->endorsedTo)
                    <div class="card mt-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-exchange-alt me-2"></i>
                                معلومات التظهير
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>مظهر إلى:</strong> {{ $check->endorsedTo?->name }}</p>
                            <p class="mb-0"><strong>تاريخ التظهير:</strong> {{ $check->endorsed_at?->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal التظهير -->
    <div class="modal fade" id="endorseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('contractor-checks.endorse', $check) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تظهير الشيك</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="endorsed_to_id" class="form-label">تظهير إلى <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="endorsed_to_id" name="endorsed_to_id" required>
                                <option value="">-- اختر المستفيد --</option>
                                @foreach ($contractors ?? [] as $contractor)
                                    @if ($contractor->id !== $check->contractor_id)
                                        <option value="{{ $contractor->id }}">{{ $contractor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="endorse_notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="endorse_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد التظهير</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal الرفض -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('contractor-checks.reject', $check) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض الشيك</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">سبب الرفض <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="rejection_reason" name="rejection_reason" required>
                                <option value="">-- اختر السبب --</option>
                                <option value="insufficient_funds">عدم كفاية الرصيد</option>
                                <option value="signature_mismatch">عدم مطابقة التوقيع</option>
                                <option value="account_closed">الحساب مغلق</option>
                                <option value="date_issue">مشكلة في التاريخ</option>
                                <option value="amount_mismatch">عدم مطابقة المبلغ</option>
                                <option value="other">سبب آخر</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reject_notes" class="form-label">ملاحظات إضافية</label>
                            <textarea class="form-control" id="reject_notes" name="notes" rows="3"></textarea>
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

    <!-- Modal التحصيل -->
    <div class="modal fade" id="collectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('contractor-checks.collect', $check) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تحصيل الشيك</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل تريد تأكيد تحصيل هذا الشيك بمبلغ <strong>{{ number_format($check->amount, 2) }} د.ع</strong>؟
                        </p>
                        <div class="mb-3">
                            <label for="collect_notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="collect_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد التحصيل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
