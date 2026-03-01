@extends('layouts.app')

@section('title', 'الحسابات المالية')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">الحسابات المالية</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createAccountModal">
                            <i class="fas fa-plus"></i> إضافة حساب
                        </button>
                    </div>
                    <div class="card-body">
                        {{-- ملخص --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5>إجمالي الحسابات</h5>
                                        <h3>{{ $summary['total_accounts'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5>إجمالي المدين</h5>
                                        <h3>{{ number_format($summary['total_debit'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5>إجمالي الدائن</h5>
                                        <h3>{{ number_format($summary['total_credit'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5>الصافي</h5>
                                        <h3>{{ number_format(($summary['total_debit'] ?? 0) - ($summary['total_credit'] ?? 0), 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الفلاتر --}}
                        <form method="GET" class="row mb-3">
                            <div class="col-md-3">
                                <select name="type" class="form-control">
                                    <option value="">كل الأنواع</option>
                                    @foreach (\App\Models\FinancialAccount::ACCOUNT_TYPES as $key => $value)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="بحث..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">بحث</button>
                            </div>
                        </form>

                        {{-- الجدول --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>رقم الحساب</th>
                                        <th>اسم الحساب</th>
                                        <th>النوع</th>
                                        <th>الفرع</th>
                                        <th>الرصيد الحالي</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accounts as $account)
                                        <tr>
                                            <td>{{ $account->id }}</td>
                                            <td>{{ $account->account_number }}</td>
                                            <td>{{ $account->account_name }}</td>
                                            <td>{{ $account->account_type_name }}</td>
                                            <td>{{ $account->branch->branch_name ?? '-' }}</td>
                                            <td
                                                class="{{ $account->current_balance > 0 ? 'text-success' : ($account->current_balance < 0 ? 'text-danger' : '') }}">
                                                {{ $account->formatted_balance }}
                                            </td>
                                            <td>
                                                @if ($account->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-secondary">معطل</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('financial.accounts.show', $account->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('financial.accounts.statement', $account->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد حسابات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $accounts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal إضافة حساب --}}
    <div class="modal fade" id="createAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('financial.accounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة حساب جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">نوع الحساب *</label>
                            <select name="account_type" class="form-control" required>
                                <option value="">اختر النوع</option>
                                @foreach (\App\Models\FinancialAccount::ACCOUNT_TYPES as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">اسم الحساب *</label>
                            <input type="text" name="account_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الفرع</label>
                            <select name="branch_id" class="form-control">
                                <option value="">بدون فرع</option>
                                @foreach (\App\Models\Branch::where('company_code', auth()->user()->company_code)->get() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الرصيد الافتتاحي</label>
                            <input type="number" name="opening_balance" class="form-control" step="0.01" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">حد الائتمان</label>
                            <input type="number" name="credit_limit" class="form-control" step="0.01" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
