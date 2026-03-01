@extends('layouts.app')

@section('page-title', 'المعاملات المالية')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold dark:text-white-light">المعاملات المالية</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">قائمة المعاملات المسجلة على الحسابات</p>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table class="table-striped table-hover w-full">
                <thead>
                    <tr>
                        <th class="text-right">التاريخ</th>
                        <th class="text-right">النوع</th>
                        <th class="text-right">الحساب</th>
                        <th class="text-right">المبلغ</th>
                        <th class="text-right">الرصيد بعد</th>
                        <th class="text-right">الحالة</th>
                        <th class="text-right">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $trx->transaction_type_name ?? $trx->transaction_type }}</td>
                            <td>{{ $trx->account->account_name ?? $trx->account->name ?? '-' }}</td>
                            <td class="font-semibold">{{ number_format($trx->amount ?? 0, 0) }} د.ع</td>
                            <td>{{ number_format($trx->balance_after ?? 0, 0) }}</td>
                            <td>
                                @if(($trx->status ?? '') === 'approved')
                                    <span class="badge bg-success/20 text-success">معتمد</span>
                                @else
                                    <span class="badge bg-warning/20 text-warning">معلق</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">{{ Str::limit($trx->description ?? '-', 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">لا توجد معاملات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
@endsection
