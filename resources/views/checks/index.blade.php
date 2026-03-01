@extends('layouts.app')

@section('title', 'إدارة الشيكات')

@section('content')
    <div class="container-fluid py-4" dir="rtl">
        {{-- الإحصائيات --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">شيكات واردة</p>
                                    <h5 class="font-weight-bolder">
                                        {{ number_format($statistics['incoming']['total_amount'] ?? 0, 2) }}</h5>
                                    <p class="text-xs text-muted mb-0">{{ $statistics['incoming']['total'] ?? 0 }} شيك</p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-arrow-down text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">شيكات صادرة</p>
                                    <h5 class="font-weight-bolder">
                                        {{ number_format($statistics['outgoing']['total_amount'] ?? 0, 2) }}</h5>
                                    <p class="text-xs text-muted mb-0">{{ $statistics['outgoing']['total'] ?? 0 }} شيك</p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="fas fa-arrow-up text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">مستحقة اليوم</p>
                                    <h5 class="font-weight-bolder text-warning">{{ $statistics['due_today'] ?? 0 }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-clock text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">متأخرة</p>
                                    <h5 class="font-weight-bolder text-danger">{{ $statistics['overdue'] ?? 0 }}</h5>
                                    <p class="text-xs text-danger mb-0">
                                        {{ number_format($statistics['overdue_amount'] ?? 0, 2) }} د.ع</p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-exclamation-triangle text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- البحث والفلترة --}}
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>قائمة الشيكات</h6>
                    <div>
                        <a href="{{ route('checks.due-today') }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-clock me-1"></i> المستحقة اليوم
                        </a>
                        <a href="{{ route('checks.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> إضافة شيك
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('checks.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..."
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="incoming" {{ ($filters['type'] ?? '') == 'incoming' ? 'selected' : '' }}>
                                    وارد</option>
                                <option value="outgoing" {{ ($filters['type'] ?? '') == 'outgoing' ? 'selected' : '' }}>
                                    صادر</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>قيد
                                    الانتظار</option>
                                <option value="deposited"
                                    {{ ($filters['status'] ?? '') == 'deposited' ? 'selected' : '' }}>مودع</option>
                                <option value="collected"
                                    {{ ($filters['status'] ?? '') == 'collected' ? 'selected' : '' }}>محصل</option>
                                <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>
                                    مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="due_from" class="form-control" placeholder="من تاريخ"
                                value="{{ $filters['due_from'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="due_to" class="form-control" placeholder="إلى تاريخ"
                                value="{{ $filters['due_to'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- جدول الشيكات --}}
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الشيك</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">البنك
                                </th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    النوع</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    المبلغ</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    الاستحقاق</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    الحالة</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checks as $check)
                                <tr class="{{ $check->is_overdue ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $check->check_number }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $check->drawer_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $check->bank_name }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $check->bank_branch }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span
                                            class="badge bg-gradient-{{ $check->check_type == 'incoming' ? 'success' : 'danger' }}">
                                            {{ $check->type_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-sm font-weight-bold">{{ number_format($check->amount, 2) }}
                                            د.ع</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-sm {{ $check->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $check->due_date->format('Y-m-d') }}
                                        </span>
                                        @if ($check->days_until_due <= 7 && $check->days_until_due > 0)
                                            <br><small class="text-warning">بعد {{ $check->days_until_due }} أيام</small>
                                        @elseif($check->is_overdue)
                                            <br><small class="text-danger">متأخر</small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span
                                            class="badge bg-gradient-{{ $check->status_color }}">{{ $check->status_label }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('checks.show', $check) }}"><i
                                                            class="fas fa-eye me-2"></i>عرض</a></li>
                                                @if ($check->status === 'pending')
                                                    <li>
                                                        <form action="{{ route('checks.deposit', $check) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item"><i
                                                                    class="fas fa-university me-2"></i>إيداع</button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if (in_array($check->status, ['pending', 'deposited']))
                                                    <li>
                                                        <form action="{{ route('checks.collect', $check) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success"><i
                                                                    class="fas fa-check me-2"></i>تحصيل</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="rejectCheck({{ $check->id }})">
                                                            <i class="fas fa-times me-2"></i>رفض
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا يوجد شيكات</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- التصفح --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $checks->appends($filters)->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal رفض الشيك --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض الشيك</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">رفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function rejectCheck(id) {
                const form = document.getElementById('rejectForm');
                form.action = `/checks/${id}/reject`;
                new bootstrap.Modal(document.getElementById('rejectModal')).show();
            }
        </script>
    @endpush
@endsection
