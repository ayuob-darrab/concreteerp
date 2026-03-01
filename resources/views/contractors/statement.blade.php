@extends('layouts.app')

@section('title', 'كشف حساب: ' . $contractor->name)

@section('content')
    <div class="container-fluid">
        <!-- معلومات المقاول -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1">{{ $contractor->name }}</h4>
                        <p class="text-muted mb-0">
                            <span class="me-3"><i class="fas fa-code me-1"></i> {{ $contractor->code }}</span>
                            <span><i class="fas fa-phone me-1"></i> {{ $contractor->phone }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div
                            class="d-inline-block text-center p-3 rounded {{ $balance >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                            <small class="d-block text-muted">الرصيد الحالي</small>
                            <h3 class="mb-0 {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($balance), 2) }} د.ع
                            </h3>
                            <small class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $balance >= 0 ? 'للمقاول' : 'على المقاول' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- فلترة -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('contractors.statement', $contractor) }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="from_date" class="form-control"
                                value="{{ request('from_date', $from_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="to_date" class="form-control"
                                value="{{ request('to_date', $to_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">نوع الحركة</label>
                            <select name="type" class="form-select">
                                <option value="">الكل</option>
                                <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>مدين</option>
                                <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>دائن</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>
                                بحث
                            </button>
                            <a href="{{ route('contractors.statement', $contractor) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- كشف الحساب -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        كشف الحساب
                    </h5>
                    <div>
                        <a href="{{ route('contractors.print-statement', $contractor) }}?{{ http_build_query(request()->all()) }}"
                            class="btn btn-outline-info" target="_blank">
                            <i class="fas fa-print me-1"></i>
                            طباعة
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>رقم المستند</th>
                                <th>البيان</th>
                                <th class="text-success">مدين</th>
                                <th class="text-danger">دائن</th>
                                <th>الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- رصيد أول المدة -->
                            <tr class="table-secondary">
                                <td colspan="3"><strong>رصيد أول المدة</strong></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <strong class="{{ $opening_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($opening_balance), 2) }}
                                        {{ $opening_balance >= 0 ? 'مدين' : 'دائن' }}
                                    </strong>
                                </td>
                            </tr>

                            @php $running_balance = $opening_balance; @endphp

                            @forelse($transactions ?? [] as $transaction)
                                @php
                                    if ($transaction->type === 'debit') {
                                        $running_balance += $transaction->amount;
                                    } else {
                                        $running_balance -= $transaction->amount;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $transaction->transaction_date?->format('Y-m-d') }}</td>
                                    <td>{{ $transaction->reference_number ?? '-' }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-success">
                                        {{ $transaction->type === 'debit' ? number_format($transaction->amount, 2) : '' }}
                                    </td>
                                    <td class="text-danger">
                                        {{ $transaction->type === 'credit' ? number_format($transaction->amount, 2) : '' }}
                                    </td>
                                    <td class="{{ $running_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($running_balance), 2) }}
                                        {{ $running_balance >= 0 ? 'مدين' : 'دائن' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted mb-0">لا توجد حركات في هذه الفترة</p>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- الإجماليات -->
                            <tr class="table-primary">
                                <td colspan="3"><strong>الإجمالي</strong></td>
                                <td class="text-success">
                                    <strong>{{ number_format($total_debit ?? 0, 2) }}</strong>
                                </td>
                                <td class="text-danger">
                                    <strong>{{ number_format($total_credit ?? 0, 2) }}</strong>
                                </td>
                                <td>
                                    <strong class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($balance), 2) }}
                                        {{ $balance >= 0 ? 'مدين' : 'دائن' }}
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ملخص -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-0">إجمالي المدين</h6>
                        <h3 class="mb-0">{{ number_format($total_debit ?? 0, 2) }} د.ع</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-0">إجمالي الدائن</h6>
                        <h3 class="mb-0">{{ number_format($total_credit ?? 0, 2) }} د.ع</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card {{ $balance >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-0">الرصيد النهائي</h6>
                        <h3 class="mb-0">
                            {{ number_format(abs($balance), 2) }} د.ع
                            <small>({{ $balance >= 0 ? 'للمقاول' : 'على المقاول' }})</small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
