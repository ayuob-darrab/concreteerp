@extends('layouts.app')

@section('title', 'الصندوق اليومي')

@section('content')
    <div class="container-fluid">
        <!-- تحذير الأيام غير المغلقة -->
        @if ($unclosedDays->count() > 0)
            <div class="alert alert-warning alert-dismissible">
                <i class="fas fa-exclamation-triangle me-2"></i>
                يوجد {{ $unclosedDays->count() }} يوم غير مغلق!
                <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#unclosedDaysModal">عرض التفاصيل</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- العنوان -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-cash-register me-2"></i>الصندوق اليومي -
                {{ \Carbon\Carbon::parse($date)->locale('ar')->format('l j F Y') }}</h4>
            <div>
                <form method="GET" class="d-inline">
                    <input type="date" name="date" class="form-control d-inline-block" style="width: auto;"
                        value="{{ $date }}" onchange="this.form.submit()">
                </form>
            </div>
        </div>

        @if ($summary)
            <div class="row">
                <!-- بطاقة الملخص -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-wallet me-2"></i>ملخص الصندوق</span>
                            <span class="badge bg-{{ $summary->is_open ? 'success' : 'secondary' }}">
                                {{ $summary->status_label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td>الرصيد الافتتاحي:</td>
                                    <td class="text-end fw-bold">{{ number_format($summary->opening_balance, 0) }}</td>
                                </tr>
                                <tr class="text-success">
                                    <td><i class="fas fa-plus-circle me-1"></i>المقبوضات ({{ $summary->receipts_count }}):
                                    </td>
                                    <td class="text-end fw-bold">+ {{ number_format($summary->total_receipts, 0) }}</td>
                                </tr>
                                <tr class="text-danger">
                                    <td><i class="fas fa-minus-circle me-1"></i>المدفوعات ({{ $summary->payments_count }}):
                                    </td>
                                    <td class="text-end fw-bold">- {{ number_format($summary->total_payments, 0) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>الرصيد الختامي:</th>
                                    <th class="text-end text-primary fs-5">
                                        {{ number_format($summary->closing_balance, 0) }}</th>
                                </tr>
                            </table>

                            @if ($summary->is_open && $date == today()->format('Y-m-d'))
                                <hr>
                                <form action="{{ route('cash.close') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظات الإقفال</label>
                                        <textarea name="notes" class="form-control" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100"
                                        onclick="return confirm('هل تريد إقفال اليوم؟')">
                                        <i class="fas fa-lock"></i> إقفال اليوم
                                    </button>
                                </form>
                            @endif

                            @if ($summary->closed_at)
                                <hr>
                                <small class="text-muted">
                                    تم الإقفال: {{ $summary->closed_at->format('Y-m-d H:i') }}
                                    @if ($summary->closedByUser)
                                        بواسطة: {{ $summary->closedByUser->name }}
                                    @endif
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- روابط سريعة -->
                    <div class="card">
                        <div class="card-header"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('receipts.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> إيصال قبض جديد
                                </a>
                                <a href="{{ route('vouchers.create') }}" class="btn btn-danger">
                                    <i class="fas fa-minus"></i> سند صرف جديد
                                </a>
                                <a href="{{ route('cash.print', ['date' => $date]) }}" class="btn btn-outline-secondary"
                                    target="_blank">
                                    <i class="fas fa-print"></i> طباعة التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل الحركات -->
                <div class="col-lg-8">
                    <!-- المقبوضات -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-arrow-down me-2"></i>
                            المقبوضات ({{ count($details['receipts']) }}) -
                            {{ number_format($details['total_receipts'], 0) }}
                        </div>
                        <div class="card-body p-0">
                            @if (count($details['receipts']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الرقم</th>
                                                <th>الوقت</th>
                                                <th>الدافع</th>
                                                <th>الوصف</th>
                                                <th class="text-end">المبلغ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($details['receipts'] as $receipt)
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="{{ route('receipts.show', $receipt) }}">{{ $receipt->receipt_number }}</a>
                                                    </td>
                                                    <td>{{ $receipt->received_at->format('H:i') }}</td>
                                                    <td>{{ $receipt->payer_name }}</td>
                                                    <td>{{ Str::limit($receipt->description, 30) }}</td>
                                                    <td class="text-end text-success fw-bold">
                                                        {{ number_format($receipt->amount_in_default, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-3 mb-0">لا توجد مقبوضات</p>
                            @endif
                        </div>
                    </div>

                    <!-- المدفوعات -->
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-arrow-up me-2"></i>
                            المدفوعات ({{ count($details['vouchers']) }}) -
                            {{ number_format($details['total_vouchers'], 0) }}
                        </div>
                        <div class="card-body p-0">
                            @if (count($details['vouchers']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الرقم</th>
                                                <th>الوقت</th>
                                                <th>المستفيد</th>
                                                <th>الوصف</th>
                                                <th class="text-end">المبلغ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($details['vouchers'] as $voucher)
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="{{ route('vouchers.show', $voucher) }}">{{ $voucher->voucher_number }}</a>
                                                    </td>
                                                    <td>{{ $voucher->paid_at->format('H:i') }}</td>
                                                    <td>{{ $voucher->payee_name }}</td>
                                                    <td>{{ Str::limit($voucher->description, 30) }}</td>
                                                    <td class="text-end text-danger fw-bold">
                                                        {{ number_format($voucher->amount_in_default, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-3 mb-0">لا توجد مدفوعات</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">لا توجد بيانات لهذا اليوم</div>
        @endif
    </div>

    <!-- Modal الأيام غير المغلقة -->
    @if ($unclosedDays->count() > 0)
        <div class="modal fade" id="unclosedDaysModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">أيام غير مغلقة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            @foreach ($unclosedDays as $day)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $day->summary_date->format('Y-m-d') }}</span>
                                    <a href="{{ route('cash.daily', ['date' => $day->summary_date->format('Y-m-d')]) }}"
                                        class="btn btn-sm btn-primary">
                                        فتح
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
