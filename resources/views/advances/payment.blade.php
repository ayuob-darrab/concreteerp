@extends('layouts.app')

@section('page-title', 'تسديد دفعة - ' . $advance->advance_number)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <i class="fas fa-money-bill-wave text-success ml-2"></i>
                    <span>تسديد دفعة للسلفة:</span>
                    <span class="text-primary font-bold">{{ $advance->advance_number }}</span>
                </h5>
                <div class="flex gap-2">
                    <a href="{{ route('advances.show', $advance) }}"
                        class="btn btn-outline-secondary flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة للسلفة</span>
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-5">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-5">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- القسم الأيسر: نموذج التسديد -->
                <div class="lg:col-span-2 space-y-6">

                    <div class="panel">
                        <div class="mb-5 flex items-center justify-between">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-plus-circle text-success ml-2"></i>
                                تسجيل دفعة جديدة
                            </h5>
                        </div>

                        <form action="{{ route('advances.payment', $advance) }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <!-- مبلغ الدفعة -->
                                <div class="space-y-3">
                                    <label class="inline-flex cursor-pointer">
                                        <span class="text-white-dark">مبلغ الدفعة <span class="text-danger">*</span></span>
                                    </label>
                                    <div class="flex">
                                        <input type="number" name="amount"
                                            class="form-input ltr:rounded-r-none rtl:rounded-l-none @error('amount') border-danger @enderror"
                                            value="{{ old('amount', $advance->remaining_amount) }}" step="0.01"
                                            min="0.01" max="{{ $advance->remaining_amount }}" required>
                                        <span
                                            class="flex items-center justify-center border border-white-light bg-[#eee] px-3 font-semibold ltr:rounded-r-md ltr:border-l-0 rtl:rounded-l-md rtl:border-r-0 dark:border-[#17263c] dark:bg-[#1b2e4b]">د.ع</span>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                    <small class="text-white-dark">الحد الأقصى:
                                        {{ number_format($advance->remaining_amount, 2) }} د.ع</small>
                                </div>

                                <!-- طريقة الدفع -->
                                <div class="space-y-3">
                                    <label class="inline-flex cursor-pointer">
                                        <span class="text-white-dark">طريقة الدفع</span>
                                    </label>
                                    <select name="payment_method" class="form-select">
                                        <option value="cash">نقداً</option>
                                        <option value="bank_transfer">تحويل بنكي</option>
                                        <option value="check">شيك</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>

                                <!-- ملاحظات -->
                                <div class="space-y-3 lg:col-span-2">
                                    <label class="inline-flex cursor-pointer">
                                        <span class="text-white-dark">ملاحظات</span>
                                    </label>
                                    <textarea name="notes" class="form-textarea" rows="3" placeholder="ملاحظات إضافية (اختياري)">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            <div class="flex justify-end gap-2">
                                <a href="{{ route('advances.show', $advance) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times ml-2"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save ml-2"></i> تسجيل الدفعة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- القسم الأيمن: معلومات السلفة والدفعات السريعة -->
                <div class="space-y-6">

                    <!-- معلومات السلفة -->
                    <div class="panel">
                        <div class="mb-5 flex items-center justify-between">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-info-circle text-primary ml-2"></i>
                                معلومات السلفة
                            </h5>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">المستفيد:</span>
                                <span class="font-semibold">{{ $advance->beneficiary_name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">مبلغ السلفة:</span>
                                <span class="font-bold text-primary">{{ number_format($advance->amount, 2) }} د.ع</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">المسدد:</span>
                                <span class="text-success font-semibold">{{ number_format($advance->paid_amount, 2) }}
                                    د.ع</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">المتبقي:</span>
                                <span class="text-danger font-bold">{{ number_format($advance->remaining_amount, 2) }}
                                    د.ع</span>
                            </div>
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">

                        <div class="flex justify-between items-center">
                            <span class="text-white-dark">نسبة السداد:</span>
                            <span class="font-semibold">{{ $advance->completion_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 mt-2">
                            <div class="bg-success h-4 rounded-full text-xs text-white text-center leading-4"
                                style="width: {{ $advance->completion_percentage }}%">
                                {{ $advance->completion_percentage }}%
                            </div>
                        </div>
                    </div>

                    <!-- الدفعات السريعة -->
                    <div class="panel">
                        <div class="mb-5 flex items-center justify-between">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-bolt text-warning ml-2"></i>
                                دفعات سريعة
                            </h5>
                        </div>

                        <div class="space-y-3">
                            <button type="button" class="btn btn-outline-success w-full"
                                onclick="setAmount({{ $advance->remaining_amount }})">
                                <i class="fas fa-check-double ml-2"></i>
                                سداد كامل ({{ number_format($advance->remaining_amount, 2) }})
                            </button>
                            @if ($advance->remaining_amount > 100)
                                <button type="button" class="btn btn-outline-primary w-full"
                                    onclick="setAmount({{ min(100, $advance->remaining_amount) }})">
                                    100 د.ع
                                </button>
                            @endif
                            @if ($advance->remaining_amount > 500)
                                <button type="button" class="btn btn-outline-primary w-full"
                                    onclick="setAmount({{ min(500, $advance->remaining_amount) }})">
                                    500 د.ع
                                </button>
                            @endif
                            @if ($advance->remaining_amount > 1000)
                                <button type="button" class="btn btn-outline-primary w-full"
                                    onclick="setAmount({{ min(1000, $advance->remaining_amount) }})">
                                    1,000 د.ع
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setAmount(amount) {
            document.querySelector('input[name="amount"]').value = amount;
        }
    </script>
@endpush
