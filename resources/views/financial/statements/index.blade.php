@extends('layouts.app')

@section('title', 'أرصدة الحسابات')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-balance-scale me-2"></i>أرصدة الحسابات</h4>
            <a href="{{ route('statements.summary') }}" class="btn btn-primary">
                <i class="fas fa-chart-pie me-1"></i> ملخص الأرصدة
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">نوع الحساب</label>
                        <select name="account_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach (\App\Models\AccountBalance::ACCOUNT_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ request('account_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع الرصيد</label>
                        <select name="balance_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="debit" {{ request('balance_type') == 'debit' ? 'selected' : '' }}>مدين (له)
                            </option>
                            <option value="credit" {{ request('balance_type') == 'credit' ? 'selected' : '' }}>دائن (عليه)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="اسم صاحب الحساب..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i></button>
                        <a href="{{ route('statements.index') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-undo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول الأرصدة -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>نوع الحساب</th>
                                <th>صاحب الحساب</th>
                                <th class="text-end">الرصيد الافتتاحي</th>
                                <th class="text-end">إجمالي المدين</th>
                                <th class="text-end">إجمالي الدائن</th>
                                <th class="text-end">الرصيد الحالي</th>
                                <th>النوع</th>
                                <th>كشف حساب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balances as $balance)
                                <tr>
                                    <td>
                                        <span
                                            class="badge bg-{{ $balance->account_type === 'contractor' ? 'info' : ($balance->account_type === 'supplier' ? 'warning' : 'secondary') }}">
                                            {{ $balance->account_type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $balance->account_name }}</strong>
                                        @if ($balance->account_phone)
                                            <br><small class="text-muted">{{ $balance->account_phone }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($balance->opening_balance, 0) }}</td>
                                    <td class="text-end text-success">{{ number_format($balance->total_debits, 0) }}</td>
                                    <td class="text-end text-danger">{{ number_format($balance->total_credits, 0) }}</td>
                                    <td
                                        class="text-end fw-bold {{ $balance->balance_type === 'debit' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($balance->current_balance), 0) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $balance->balance_type === 'debit' ? 'success' : 'danger' }}">
                                            {{ $balance->balance_type === 'debit' ? 'له' : 'عليه' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($balance->account_type === 'contractor')
                                            <a href="{{ route('statements.contractor', ['id' => $balance->account_id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        @elseif($balance->account_type === 'supplier')
                                            <a href="{{ route('statements.supplier', ['id' => $balance->account_id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        @elseif($balance->account_type === 'employee')
                                            <a href="{{ route('statements.employee', ['id' => $balance->account_id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد أرصدة</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $balances->links() }}
            </div>
        </div>
    </div>
@endsection
