@extends('layouts.app')

@section('page-title', 'تفاصيل بطاقة الدفع')

@section('content')
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- معلومات البطاقة -->
        <div class="panel lg:col-span-1">
            <div class="mb-5 flex items-center justify-between">
                <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
                <div class="flex gap-2">
                    <a href="{{ route('company-payment-cards.edit', $card->id) }}" class="btn btn-sm btn-outline-warning">✏️</a>
                    <form action="{{ route('company-payment-cards.toggle-status', $card->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-{{ $card->is_active ? 'danger' : 'success' }}">
                            {{ $card->is_active ? '🔒 تعطيل' : '🔓 تفعيل' }}
                        </button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif

            <div class="text-center mb-5">
                <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-3xl">💳</span>
                </div>
                <h4 class="text-xl font-bold">{{ $card->card_name }}</h4>
                <p class="text-gray-500">{{ $card->card_type_name }}</p>
                <span class="badge bg-{{ $card->status_color }} mt-2">{{ $card->status_text }}</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">صاحب البطاقة:</span><span class="font-semibold">{{ $card->holder_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">رقم البطاقة:</span><span class="font-mono">{{ $card->card_number_masked }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الفرع:</span><span>{{ $card->branch->branch_name ?? 'عام' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">تاريخ الانتهاء:</span><span>{{ $card->expiry_date ? $card->expiry_date->format('Y-m-d') : '-' }}</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-gray-500">الرصيد الافتتاحي:</span><span>{{ number_format($card->opening_balance, 0) }} دينار</span></div>
                <div class="flex justify-between text-lg font-bold"><span>الرصيد الحالي:</span><span class="text-{{ $card->current_balance > 0 ? 'success' : 'danger' }}">{{ number_format($card->current_balance, 0) }} دينار</span></div>
                <hr>
                <div class="flex justify-between text-success"><span>إجمالي الإيداعات:</span><span>{{ number_format($stats['total_deposits'], 0) }} دينار</span></div>
                <div class="flex justify-between text-danger"><span>إجمالي السحوبات:</span><span>{{ number_format($stats['total_withdrawals'], 0) }} دينار</span></div>
                <div class="flex justify-between"><span class="text-gray-500">عدد المعاملات:</span><span>{{ $stats['transactions_count'] }}</span></div>
            </div>

            <!-- إيداع / سحب -->
            <div class="mt-5 space-y-3">
                <form action="{{ route('company-payment-cards.deposit', $card->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount" class="form-input flex-1" placeholder="مبلغ الإيداع" required min="1">
                    <button type="submit" class="btn btn-success btn-sm">إيداع</button>
                </form>
                <form action="{{ route('company-payment-cards.withdraw', $card->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount" class="form-input flex-1" placeholder="مبلغ السحب" required min="1" max="{{ $card->current_balance }}">
                    <button type="submit" class="btn btn-danger btn-sm">سحب</button>
                </form>
            </div>
        </div>

        <!-- سجل المعاملات -->
        <div class="panel lg:col-span-2">
            <h5 class="mb-5 text-lg font-semibold">📋 سجل المعاملات</h5>

            <div class="table-responsive">
                <table class="table-striped table-hover">
                    <thead>
                        <tr>
                            <th>رقم المعاملة</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>الرصيد قبل</th>
                            <th>الرصيد بعد</th>
                            <th>الوصف</th>
                            <th>بواسطة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="font-mono text-sm">{{ $transaction->transaction_number }}</td>
                                <td><span class="badge bg-{{ $transaction->type_color }}">{{ $transaction->type_name }}</span></td>
                                <td class="font-bold text-{{ $transaction->type_color }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                </td>
                                <td>{{ number_format($transaction->balance_before, 0) }}</td>
                                <td>{{ number_format($transaction->balance_after, 0) }}</td>
                                <td>{{ $transaction->description ?? '-' }}</td>
                                <td>{{ $transaction->creator->fullname ?? '-' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-5 text-gray-500">لا توجد معاملات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
@endsection
