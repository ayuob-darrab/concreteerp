@extends('layouts.app')

@section('page-title', 'تقرير معاملات البطاقات')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold dark:text-white-light">📊 تقرير معاملات البطاقات</h5>
            <a href="{{ route('company-payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
        </div>

        <!-- فلاتر -->
        <form method="GET" class="mb-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="mb-1 block text-sm">البطاقة</label>
                    <select name="card_id" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        @foreach ($cards as $card)
                            <option value="{{ $card->id }}" {{ request('card_id') == $card->id ? 'selected' : '' }}>{{ $card->card_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm">الفرع</label>
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm">النوع</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>إيداع</option>
                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>سحب</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm">من تاريخ</label>
                    <input type="date" name="date_from" class="form-input form-input-sm" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-input form-input-sm" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">🔍 بحث</button>
                <a href="{{ route('company-payment-cards.transactions') }}" class="btn btn-outline-secondary btn-sm">إعادة تعيين</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>البطاقة</th>
                        <th>الفرع</th>
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
                    @forelse ($transactions as $t)
                        <tr>
                            <td class="font-mono text-sm">{{ $t->transaction_number }}</td>
                            <td>{{ $t->paymentCard->card_name ?? '-' }}</td>
                            <td>{{ $t->branch->branch_name ?? 'عام' }}</td>
                            <td><span class="badge bg-{{ $t->type_color }}">{{ $t->type_name }}</span></td>
                            <td class="font-bold text-{{ $t->type_color }}">
                                {{ $t->type === 'deposit' ? '+' : '-' }}{{ number_format($t->amount, 0) }}
                            </td>
                            <td>{{ number_format($t->balance_before, 0) }}</td>
                            <td>{{ number_format($t->balance_after, 0) }}</td>
                            <td>{{ $t->description ?? '-' }}</td>
                            <td>{{ $t->creator->fullname ?? '-' }}</td>
                            <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-gray-500">لا توجد معاملات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $transactions->appends(request()->query())->links() }}</div>
    </div>
@endsection
