@extends('layouts.app')

@section('page-title', 'طلب عمل #' . $order->id)

@section('content')
    <div class="panel mt-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold dark:text-white">طلب #{{ $order->id }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('work-orders.index') }}" class="btn btn-outline-primary btn-sm">القائمة</a>
                <a href="{{ route('work-orders.print', $order) }}" target="_blank" class="btn btn-outline-secondary btn-sm">طباعة</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->has('error'))
            <div class="alert alert-danger mb-4">{{ $errors->first('error') }}</div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                <div class="text-sm text-gray-500">الحالة</div>
                <div class="font-semibold">{{ $order->status_label ?: '—' }}</div>
            </div>
            <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                <div class="text-sm text-gray-500">الكمية</div>
                <div class="font-semibold">{{ $order->quantity }}</div>
            </div>
            <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                <div class="text-sm text-gray-500">الفرع</div>
                <div class="font-semibold">{{ $order->branch->branch_name ?? '—' }}</div>
            </div>
        </div>

        @if (isset($report['statistics']))
            <div class="mb-6 rounded border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="mb-3 font-semibold">إحصائيات التنفيذ</h3>
                <div class="grid grid-cols-2 gap-2 text-sm md:grid-cols-4">
                    <div>منفذ: {{ $report['statistics']['executed_quantity'] ?? '—' }}</div>
                    <div>متبقي: {{ $report['statistics']['remaining_quantity'] ?? '—' }}</div>
                    <div>نسبة: {{ $report['statistics']['execution_percentage'] ?? '—' }}%</div>
                    <div>مبلغ: {{ $report['statistics']['total_amount'] ?? '—' }}</div>
                </div>
            </div>
        @endif

        <div class="flex flex-wrap gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
            @can('review', $order)
                <form action="{{ route('work-orders.review', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">تحويل للمراجعة</button>
                </form>
            @endcan
            @can('approve', $order)
                <form action="{{ route('work-orders.approve', $order) }}" method="POST" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <input type="text" name="notes" placeholder="ملاحظات (اختياري)" class="form-input form-input-sm" value="{{ old('notes') }}">
                    <button type="submit" class="btn btn-success btn-sm">موافقة</button>
                </form>
            @endcan
            @can('reject', $order)
                <form action="{{ route('work-orders.reject', $order) }}" method="POST" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <input type="text" name="notes" placeholder="سبب الرفض" class="form-input form-input-sm" required value="{{ old('notes') }}">
                    <button type="submit" class="btn btn-danger btn-sm">رفض</button>
                </form>
            @endcan
            @can('schedule', $order)
                <form action="{{ route('work-orders.schedule', $order) }}" method="POST" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <input type="datetime-local" name="delivery_datetime" class="form-input form-input-sm" required value="{{ old('delivery_datetime') }}">
                    <button type="submit" class="btn btn-primary btn-sm">جدولة</button>
                </form>
            @endcan
            @can('cancel', $order)
                <form action="{{ route('work-orders.cancel', $order) }}" method="POST" class="flex flex-wrap items-end gap-2" onsubmit="return confirm('تأكيد إلغاء الطلب؟');">
                    @csrf
                    <input type="text" name="reason" placeholder="سبب الإلغاء" class="form-input form-input-sm" required value="{{ old('reason') }}">
                    <button type="submit" class="btn btn-outline-danger btn-sm">إلغاء الطلب</button>
                </form>
            @endcan
        </div>
    </div>
@endsection
