@extends('layouts.app')

@section('page-title', 'طلب عمل جديد')

@section('content')
    <div class="panel mt-6 max-w-4xl">
        <h2 class="mb-6 text-lg font-semibold dark:text-white">إنشاء طلب عمل</h2>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 list-inside list-disc">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('work-orders.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="sender_id" value="{{ auth()->id() }}">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block">نوع المرسل <span class="text-danger">*</span></label>
                    <select name="sender_type" class="form-select" required>
                        <option value="">— اختر —</option>
                        @foreach ($senderTypes as $st)
                            <option value="{{ $st->code }}" @selected(old('sender_type') === $st->code)>{{ $st->typename }} ({{ $st->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block">الخلطة <span class="text-danger">*</span></label>
                    <select name="classification" class="form-select" required>
                        <option value="">— اختر —</option>
                        @foreach ($concreteMixes as $mix)
                            <option value="{{ $mix->id }}" @selected(old('classification') == $mix->id)>#{{ $mix->id }} @if($mix->classification) — {{ $mix->classification }} @endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block">الشركة <span class="text-danger">*</span></label>
                    <select name="company_code" class="form-select" required>
                        @foreach ($companies as $c)
                            <option value="{{ $c->code }}" @selected(old('company_code', $companyCode) === $c->code)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block">الفرع <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select" required>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}" @selected(old('branch_id', $branchId) == $b->id)>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block">الكمية (م³) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity') }}" class="form-input" required>
                </div>
                <div>
                    <label class="mb-1 block">موعد التسليم</label>
                    <input type="datetime-local" name="delivery_datetime" value="{{ old('delivery_datetime') }}" class="form-input">
                </div>
                <div>
                    <label class="mb-1 block">اسم العميل</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-input">
                </div>
                <div>
                    <label class="mb-1 block">هاتف العميل</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input">
                </div>
                <div>
                    <label class="mb-1 block">سعر أولي</label>
                    <input type="number" step="0.01" min="0" name="initial_price" value="{{ old('initial_price') }}" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block">الموقع</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="form-input w-full">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block">ملاحظات</label>
                    <textarea name="notes" rows="3" class="form-textarea w-full">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="btn btn-primary">حفظ الطلب</button>
                <a href="{{ route('work-orders.index') }}" class="btn btn-outline-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
