@extends('layouts.app')

@section('title', 'إدارة المقاولين')

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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">إجمالي المقاولين</p>
                                    <h5 class="font-weight-bolder">{{ number_format($statistics['total']) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-users text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">النشطين</p>
                                    <h5 class="font-weight-bolder text-success">{{ number_format($statistics['active']) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-check text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">المحظورين</p>
                                    <h5 class="font-weight-bolder text-danger">{{ number_format($statistics['blocked']) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="fas fa-ban text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">إجمالي الأرصدة</p>
                                    <h5 class="font-weight-bolder">{{ number_format($statistics['total_balance'], 2) }} د.ع
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-wallet text-lg opacity-10"></i>
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
                    <h6>قائمة المقاولين</h6>
                    <a href="{{ route('contractors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> إضافة مقاول
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('contractors.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..."
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>نشط
                                </option>
                                <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>
                                    غير نشط</option>
                                <option value="blocked" {{ ($filters['status'] ?? '') == 'blocked' ? 'selected' : '' }}>
                                    محظور</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="classification" class="form-select">
                                <option value="">كل التصنيفات</option>
                                <option value="A" {{ ($filters['classification'] ?? '') == 'A' ? 'selected' : '' }}>
                                    تصنيف A</option>
                                <option value="B" {{ ($filters['classification'] ?? '') == 'B' ? 'selected' : '' }}>
                                    تصنيف B</option>
                                <option value="C" {{ ($filters['classification'] ?? '') == 'C' ? 'selected' : '' }}>
                                    تصنيف C</option>
                                <option value="D" {{ ($filters['classification'] ?? '') == 'D' ? 'selected' : '' }}>
                                    تصنيف D</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="contractor_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="individual"
                                    {{ ($filters['contractor_type'] ?? '') == 'individual' ? 'selected' : '' }}>فرد
                                </option>
                                <option value="company"
                                    {{ ($filters['contractor_type'] ?? '') == 'company' ? 'selected' : '' }}>شركة</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search me-1"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>

                {{-- جدول المقاولين --}}
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المقاول</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">التصنيف
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    الحالة</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    الرصيد</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    الطلبات</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contractors as $contractor)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $contractor->contractor_name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $contractor->code }} |
                                                    {{ $contractor->phone }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-gradient-{{ $contractor->classification == 'A' ? 'success' : ($contractor->classification == 'B' ? 'info' : ($contractor->classification == 'C' ? 'warning' : 'secondary')) }}">
                                            {{ $contractor->classification ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span
                                            class="badge bg-gradient-{{ $contractor->status_color }}">{{ $contractor->status_label }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ number_format($contractor->account?->current_balance ?? 0, 2) }} د.ع
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span
                                            class="text-secondary text-xs font-weight-bold">{{ $contractor->total_orders ?? 0 }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('contractors.show', $contractor) }}"><i
                                                            class="fas fa-eye me-2"></i>عرض</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('contractors.edit', $contractor) }}"><i
                                                            class="fas fa-edit me-2"></i>تعديل</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('contractors.statement', $contractor) }}"><i
                                                            class="fas fa-file-invoice me-2"></i>كشف الحساب</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @if ($contractor->status !== 'blocked')
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="blockContractor({{ $contractor->id }})">
                                                            <i class="fas fa-ban me-2"></i>حظر
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('contractors.unblock', $contractor) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="fas fa-check me-2"></i>رفع الحظر
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا يوجد مقاولين</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- التصفح --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $contractors->appends($filters)->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal حظر المقاول --}}
    <div class="modal fade" id="blockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="blockForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">حظر المقاول</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">سبب الحظر <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حظر</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function blockContractor(id) {
                const form = document.getElementById('blockForm');
                form.action = `/contractors/${id}/block`;
                new bootstrap.Modal(document.getElementById('blockModal')).show();
            }
        </script>
    @endpush
@endsection
