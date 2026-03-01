@extends('layouts.app')

@section('title', 'نظام الرواتب')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="fas fa-money-check-alt text-success"></i>
                كشوفات الرواتب - {{ $month }}/{{ $year }}
            </h3>
            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                    <i class="fas fa-sync"></i> إنشاء كشوفات جماعية
                </button>
                <a href="{{ route('payroll.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> كشف راتب جديد
                </a>
            </div>
        </div>

        <!-- فلتر الشهر -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('payroll.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">السنة</label>
                        <select name="year" class="form-select">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الشهر</label>
                        <select name="month" class="form-select">
                            @foreach (['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $i => $name)
                                <option value="{{ $i + 1 }}" {{ $month == $i + 1 ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> تصفية
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- إحصائيات -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h4>{{ number_format($statistics['total_count']) }}</h4>
                        <p class="mb-0">إجمالي الكشوفات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h4>{{ number_format($statistics['total_net']) }}</h4>
                        <p class="mb-0">إجمالي الصافي</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h4>{{ $statistics['draft_count'] }}</h4>
                        <p class="mb-0">مسودات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4>{{ $statistics['paid_count'] }}</h4>
                        <p class="mb-0">مدفوعة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الكشوفات -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>الراتب الأساسي</th>
                                <th>البدلات</th>
                                <th>الخصومات</th>
                                <th>الصافي</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $payroll->employee->name ?? '-' }}</td>
                                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td class="text-success">+{{ number_format($payroll->total_additions, 2) }}</td>
                                    <td class="text-danger">-{{ number_format($payroll->total_deductions, 2) }}</td>
                                    <td><strong>{{ number_format($payroll->net_salary, 2) }}</strong></td>
                                    <td>
                                        @switch($payroll->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">مسودة</span>
                                            @break

                                            @case('approved')
                                                <span class="badge bg-info">معتمد</span>
                                            @break

                                            @case('paid')
                                                <span class="badge bg-success">مدفوع</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($payroll->status == 'draft')
                                                <form action="{{ route('payroll.approve', $payroll) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success"
                                                        onclick="return confirm('اعتماد الكشف؟')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($payroll->status == 'approved')
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#payModal{{ $payroll->id }}">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('payroll.print', $payroll) }}"
                                                class="btn btn-outline-secondary" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>

                                        <!-- Modal الدفع -->
                                        <div class="modal fade" id="payModal{{ $payroll->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('payroll.pay', $payroll) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">صرف الراتب</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">طريقة الدفع</label>
                                                                <select name="payment_method" class="form-select"
                                                                    required>
                                                                    <option value="cash">نقداً</option>
                                                                    <option value="bank_transfer">تحويل بنكي</option>
                                                                    <option value="check">شيك</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">رقم المرجع (اختياري)</label>
                                                                <input type="text" name="payment_reference"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-success">صرف</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد كشوفات رواتب</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $payrolls->links() }}
                </div>
            </div>
        </div>

        <!-- Modal إنشاء جماعي -->
        <div class="modal fade" id="bulkGenerateModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('payroll.generate-bulk') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">إنشاء كشوفات رواتب جماعية</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                سيتم إنشاء كشوفات رواتب لجميع الموظفين النشطين
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">السنة</label>
                                    <select name="year" class="form-select" required>
                                        @for ($y = now()->year; $y >= 2020; $y--)
                                            <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الشهر</label>
                                    <select name="month" class="form-select" required>
                                        @foreach (['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $i => $name)
                                            <option value="{{ $i + 1 }}"
                                                {{ now()->month == $i + 1 ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success">إنشاء الكشوفات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
