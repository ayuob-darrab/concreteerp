@extends('layouts.app')

@section('title', 'سندات بانتظار الموافقة')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                <i class="fas fa-clock text-warning me-2"></i>
                سندات بانتظار الموافقة
                <span class="badge bg-warning">{{ $vouchers->total() }}</span>
            </h4>
            <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع
            </a>
        </div>

        @if ($vouchers->count() > 0)
            <div class="row">
                @foreach ($vouchers as $voucher)
                    <div class="col-lg-6 mb-4">
                        <div class="card border-warning">
                            <div
                                class="card-header bg-warning bg-opacity-25 d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>{{ $voucher->voucher_number }}</strong>
                                    <small class="text-muted ms-2">{{ $voucher->created_at->diffForHumans() }}</small>
                                </span>
                                <span
                                    class="badge bg-{{ $voucher->payee_type === 'employee' ? 'primary' : ($voucher->payee_type === 'contractor' ? 'info' : 'secondary') }}">
                                    {{ $voucher->payee_type_label }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="card-title">{{ $voucher->payee_name }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($voucher->description, 100) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $voucher->creator->name ?? '' }}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-building me-1"></i>{{ $voucher->branch->name ?? '' }}
                                        </small>
                                    </div>
                                    <div class="col-4 text-end">
                                        <h3 class="text-danger">{{ number_format($voucher->amount, 0) }}</h3>
                                        <small class="text-muted">{{ $voucher->currency->symbol ?? 'د.ع' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100">
                                    <a href="{{ route('vouchers.show', $voucher) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> التفاصيل
                                    </a>
                                    <form action="{{ route('vouchers.approve', $voucher) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirm('موافقة على السند؟')">
                                            <i class="fas fa-check me-1"></i> موافقة
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal{{ $voucher->id }}">
                                        <i class="fas fa-times me-1"></i> رفض
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal الرفض -->
                    <div class="modal fade" id="rejectModal{{ $voucher->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('vouchers.reject', $voucher) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">رفض سند {{ $voucher->voucher_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>المستفيد: <strong>{{ $voucher->payee_name }}</strong></p>
                                        <p>المبلغ: <strong class="text-danger">{{ $voucher->formatted_amount }}</strong>
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="يرجى ذكر سبب رفض السند..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $vouchers->links() }}
        @else
            <div class="alert alert-success text-center py-5">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h4>لا توجد سندات بانتظار الموافقة</h4>
            </div>
        @endif
    </div>
@endsection
