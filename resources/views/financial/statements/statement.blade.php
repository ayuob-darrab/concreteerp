@extends('layouts.app')

@section('title', 'كشف حساب - ' . $balance->account_name)

@section('content')
    <div class="container-fluid">
        <!-- رأس كشف الحساب -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1">{{ $balance->account_name }}</h4>
                        <span
                            class="badge bg-{{ $balance->account_type === 'contractor' ? 'info' : ($balance->account_type === 'supplier' ? 'warning' : 'secondary') }}">
                            {{ $balance->account_type_label }}
                        </span>
                        @if ($balance->account_phone)
                            <span class="ms-2 text-muted">
                                <i class="fas fa-phone me-1"></i>{{ $balance->account_phone }}
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <div
                            class="d-inline-block text-center px-4 py-2 rounded {{ $balance->balance_type === 'debit' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                            <small class="d-block text-muted">الرصيد الحالي</small>
                            <h3 class="mb-0 {{ $balance->balance_type === 'debit' ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($balance->current_balance), 0) }}
                                <small>{{ $balance->balance_type === 'debit' ? '(له)' : '(عليه)' }}</small>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- فلاتر الفترة -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date', $fromDate) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> عرض
                        </button>
                        <a href="{{ route('statements.print', ['type' => $balance->account_type, 'id' => $balance->account_id, 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
                            class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-print me-1"></i> طباعة
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- ملخص الفترة -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">رصيد أول المدة</small>
                        <h4>{{ number_format($openingBalance, 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10">
                    <div class="card-body text-center">
                        <small class="text-muted">إجمالي المدين (له)</small>
                        <h4 class="text-success">{{ number_format($periodDebits, 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger bg-opacity-10">
                    <div class="card-body text-center">
                        <small class="text-muted">إجمالي الدائن (عليه)</small>
                        <h4 class="text-danger">{{ number_format($periodCredits, 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary bg-opacity-10">
                    <div class="card-body text-center">
                        <small class="text-muted">رصيد آخر المدة</small>
                        <h4 class="text-primary">{{ number_format($closingBalance, 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الحركات -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>حركات الحساب
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>المستند</th>
                                <th>البيان</th>
                                <th class="text-end">مدين (له)</th>
                                <th class="text-end">دائن (عليه)</th>
                                <th class="text-end">الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- رصيد أول المدة -->
                            <tr class="table-secondary">
                                <td>{{ $fromDate }}</td>
                                <td>-</td>
                                <td><strong>رصيد أول المدة</strong></td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end fw-bold">{{ number_format($openingBalance, 0) }}</td>
                            </tr>
                            @php $runningBalance = $openingBalance; @endphp
                            @forelse($transactions as $transaction)
                                @php
                                    $runningBalance += $transaction->debit - $transaction->credit;
                                @endphp
                                <tr>
                                    <td>{{ $transaction->date->format('Y-m-d') }}</td>
                                    <td>
                                        @if ($transaction->document_type === 'receipt')
                                            <a href="{{ route('receipts.show', $transaction->document_id) }}">
                                                <span class="badge bg-success">قبض</span>
                                                {{ $transaction->document_number }}
                                            </a>
                                        @elseif($transaction->document_type === 'voucher')
                                            <a href="{{ route('vouchers.show', $transaction->document_id) }}">
                                                <span class="badge bg-danger">صرف</span>
                                                {{ $transaction->document_number }}
                                            </a>
                                        @else
                                            {{ $transaction->document_number }}
                                        @endif
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-end text-success">
                                        {{ $transaction->debit > 0 ? number_format($transaction->debit, 0) : '-' }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ $transaction->credit > 0 ? number_format($transaction->credit, 0) : '-' }}
                                    </td>
                                    <td
                                        class="text-end fw-bold {{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($runningBalance), 0) }}
                                        {{ $runningBalance >= 0 ? '(له)' : '(عليه)' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        لا توجد حركات في هذه الفترة
                                    </td>
                                </tr>
                            @endforelse
                            <!-- رصيد آخر المدة -->
                            <tr class="table-primary">
                                <td>{{ $toDate }}</td>
                                <td>-</td>
                                <td><strong>رصيد آخر المدة</strong></td>
                                <td class="text-end fw-bold text-success">{{ number_format($periodDebits, 0) }}</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($periodCredits, 0) }}</td>
                                <td class="text-end fw-bold">{{ number_format($closingBalance, 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
