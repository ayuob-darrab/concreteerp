@extends('layouts.app')

@section('page-title', 'إعدادات أسعار الاشتراكات')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <!-- رأس الصفحة -->
        <div class="panel">
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('subscriptions.companies') }}" class="btn btn-outline-secondary btn-sm">
                        <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        رجوع
                    </a>
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="inline-block h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        إعدادات أسعار الاشتراكات
                    </h5>
                </div>
            </div>
        </div>

        <!-- رسائل النجاح والخطأ -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <!-- الإعدادات العامة -->
            <div class="panel">
                <div class="mb-5 flex items-center">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="inline-block h-5 w-5 mr-2 text-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        الأسعار الافتراضية
                    </h5>
                </div>

                <form action="{{ route('subscriptions.settings.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <!-- سعر المستخدم الشهري (والسنوي = الشهري × 12) -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">سعر المستخدم الشهري <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="standard_price_monthly"
                                    value="{{ old('standard_price_monthly', $settings->standard_price_monthly) }}"
                                    class="form-input rounded-l-none flex-1" step="0.01" min="0" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    دينار/شهر
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">السعر الافتراضي لكل مستخدم شهرياً. السنوي = نفس السعر × 12 شهر</p>
                        </div>

                        <!-- نسبة الطلبات -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">نسبة الطلبات الافتراضية <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="default_percentage_rate"
                                    value="{{ old('default_percentage_rate', $settings->default_percentage_rate) }}"
                                    class="form-input rounded-l-none flex-1" step="0.01" min="0" max="100"
                                    required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    %
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">نسبة الخصم من كل طلب (لخطة نسبة من الطلبات)</p>
                        </div>

                        <!-- مبلغ ثابت للطلب -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">مبلغ ثابت لكل طلب <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="default_fixed_order_fee"
                                    value="{{ old('default_fixed_order_fee', $settings->default_fixed_order_fee) }}"
                                    class="form-input rounded-l-none flex-1" step="0.01" min="0" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    دينار
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">مبلغ ثابت يخصم من كل طلب (بديل للنسبة)</p>
                        </div>
                    </div>

                    <hr class="my-5 border-[#e0e6ed] dark:border-[#1b2e4b]">

                    <h6 class="text-md font-semibold mb-4 text-gray-700 dark:text-gray-300">
                        <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        إعدادات فترة السماح والتنبيهات
                    </h6>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                        <!-- فترة السماح -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">فترة السماح <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="grace_period_days"
                                    value="{{ old('grace_period_days', $settings->grace_period_days) }}"
                                    class="form-input rounded-l-none flex-1" min="1" max="30" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    يوم
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">المدة المسموحة بعد انتهاء الاشتراك قبل التعطيل</p>
                        </div>

                        <!-- أيام التحذير -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">أيام التحذير <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="warning_days"
                                    value="{{ old('warning_days', $settings->warning_days) }}"
                                    class="form-input rounded-l-none flex-1" min="1" max="15" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    يوم
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">قبل كم يوم من انتهاء الاشتراك يظهر التحذير</p>
                        </div>

                        <!-- مهلة السداد -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">مهلة السداد <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="payment_due_days"
                                    value="{{ old('payment_due_days', $settings->payment_due_days) }}"
                                    class="form-input rounded-l-none flex-1" min="1" max="30" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    يوم
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">المهلة المعطاة للسداد بعد إصدار الفاتورة</p>
                        </div>

                        <!-- أيام الاشتراك التجريبي -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">أيام الاشتراك التجريبي <span
                                    class="text-danger">*</span></label>
                            <div class="flex">
                                <input type="number" name="trial_days"
                                    value="{{ old('trial_days', $settings->trial_days ?? 7) }}"
                                    class="form-input rounded-l-none flex-1" min="1" max="365" required>
                                <span
                                    class="flex items-center justify-center border border-l-0 border-[#e0e6ed] bg-[#f1f2f3] px-3 font-semibold dark:border-[#17263c] dark:bg-[#1b2e4b]">
                                    يوم
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">المدة الافتراضية للاشتراك التجريبي (قابل للتعديل عند إنشاء كل اشتراك تجريبي)</p>
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="mt-5">
                        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3" class="form-textarea w-full" placeholder="أي ملاحظات إضافية...">{{ old('notes', $settings->notes) }}</textarea>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">
                            <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>

            <!-- ملخص الإعدادات الحالية -->
            <div class="panel">
                <div class="mb-5 flex items-center">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="inline-block h-5 w-5 mr-2 text-info" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        ملخص الإعدادات الحالية
                    </h5>
                </div>

                <div class="space-y-4">
                    <!-- بطاقة السعر الشهري -->
                    <div class="rounded-lg border border-primary/30 bg-primary/5 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-primary/20 p-2">
                                    <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold">الاشتراك الشهري</h6>
                                    <p class="text-sm text-gray-500">سعر المستخدم الواحد</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span
                                    class="text-2xl font-bold text-primary">{{ number_format($settings->standard_price_monthly, 0) }}</span>
                                <span class="text-sm text-gray-500">دينار/شهر</span>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة السعر السنوي (نفس السعر الشهري × 12 شهر) -->
                    <div class="rounded-lg border border-success/30 bg-success/5 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-success/20 p-2">
                                    <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold">الاشتراك السنوي</h6>
                                    <p class="text-sm text-gray-500">نفس السعر الشهري × 12 شهر</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="text-2xl font-bold text-success">{{ number_format(($settings->standard_price_monthly ?? 0) * 12, 0) }}</span>
                                <span class="text-sm text-gray-500">دينار/سنوياً</span>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة فترة السماح -->
                    <div class="rounded-lg border border-warning/30 bg-warning/5 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-warning/20 p-2">
                                    <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold">فترة السماح</h6>
                                    <p class="text-sm text-gray-500">بعد انتهاء الاشتراك</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="text-2xl font-bold text-warning">{{ $settings->grace_period_days }}</span>
                                <span class="text-sm text-gray-500">يوم</span>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة التحذير -->
                    <div class="rounded-lg border border-info/30 bg-info/5 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-info/20 p-2">
                                    <svg class="h-6 w-6 text-info" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold">التحذير قبل الانتهاء</h6>
                                    <p class="text-sm text-gray-500">يظهر تنبيه للمستخدم</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="text-2xl font-bold text-info">{{ $settings->warning_days }}</span>
                                <span class="text-sm text-gray-500">يوم</span>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة الاشتراك التجريبي -->
                    <div class="rounded-lg border border-primary/30 bg-primary/5 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-primary/20 p-2">
                                    <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold">الاشتراك التجريبي</h6>
                                    <p class="text-sm text-gray-500">المدة الافتراضية بالأيام</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="text-2xl font-bold text-primary">{{ $settings->trial_days ?? 7 }}</span>
                                <span class="text-sm text-gray-500">يوم</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأسعار الخاصة بالشركات -->
        <div class="panel">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <svg class="inline-block h-5 w-5 mr-2 text-secondary" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    الأسعار الخاصة بالشركات
                </h5>
                <button type="button" class="btn btn-primary btn-sm" x-data
                    @click="$dispatch('open-modal', 'add-company-price')">
                    <svg class="h-5 w-5 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة سعر خاص
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th>الشركة</th>
                            <th>الكود</th>
                            <th>السعر الشهري</th>
                            <th>السعر السنوي</th>
                            <th>نسبة الطلبات</th>
                            <th>مبلغ ثابت/طلب</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            @php
                                $pricing = $companyPrices[$company->code] ?? null;
                            @endphp
                            @if ($pricing)
                                <tr>
                                    <td class="font-semibold">{{ $company->name }}</td>
                                    <td><span class="badge badge-outline-secondary">{{ $company->code }}</span></td>
                                    <td>
                                        @if ($pricing->price_per_user_monthly)
                                            <span
                                                class="text-primary font-semibold">{{ number_format($pricing->price_per_user_monthly, 0) }}</span>
                                            دينار
                                        @else
                                            <span class="text-gray-400">افتراضي</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pricing->price_per_user_yearly)
                                            <span
                                                class="text-success font-semibold">{{ number_format($pricing->price_per_user_yearly, 0) }}</span>
                                            دينار
                                        @else
                                            <span class="text-gray-400">افتراضي</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pricing->custom_percentage_rate)
                                            <span
                                                class="text-info font-semibold">{{ $pricing->custom_percentage_rate }}%</span>
                                        @else
                                            <span class="text-gray-400">افتراضي</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pricing->custom_fixed_order_fee)
                                            <span
                                                class="text-warning font-semibold">{{ number_format($pricing->custom_fixed_order_fee, 0) }}</span>
                                            دينار
                                        @else
                                            <span class="text-gray-400">افتراضي</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pricing->is_active)
                                            <span class="badge badge-outline-success">نشط</span>
                                        @else
                                            <span class="badge badge-outline-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="max-w-[200px] truncate">{{ $pricing->notes ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" x-data
                                                @click="$dispatch('open-modal', 'edit-price-{{ $company->code }}')"
                                                title="تعديل">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form
                                                action="{{ route('subscriptions.company-pricing.delete', $company->code) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف السعر الخاص لهذه الشركة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="حذف">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Modal تعديل السعر -->
                                        <div x-data="{ open: false }"
                                            x-on:open-modal.window="if ($event.detail === 'edit-price-{{ $company->code }}') open = true"
                                            x-on:close-modal.window="open = false" x-show="open"
                                            class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
                                            <div class="flex min-h-screen items-center justify-center px-4">
                                                <div class="fixed inset-0 bg-black/60" @click="open = false"></div>
                                                <div
                                                    class="panel relative w-full max-w-lg rounded-lg border-0 p-0 overflow-hidden">
                                                    <div
                                                        class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3 dark:bg-[#121c2c]">
                                                        <h5 class="text-lg font-bold">تعديل سعر {{ $company->name }}</h5>
                                                        <button type="button" class="text-white-dark hover:text-dark"
                                                            @click="open = false">✕</button>
                                                    </div>
                                                    <form
                                                        action="{{ route('subscriptions.company-pricing.update', $company->code) }}"
                                                        method="POST" class="p-5">
                                                        @csrf
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm mb-1">السعر الشهري</label>
                                                                <input type="number" name="price_per_user_monthly"
                                                                    value="{{ $pricing->price_per_user_monthly }}"
                                                                    class="form-input" step="0.01" min="0"
                                                                    placeholder="اتركه فارغاً للافتراضي">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm mb-1">السعر السنوي</label>
                                                                <input type="number" name="price_per_user_yearly"
                                                                    value="{{ $pricing->price_per_user_yearly }}"
                                                                    class="form-input" step="0.01" min="0"
                                                                    placeholder="اتركه فارغاً للافتراضي">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm mb-1">نسبة الطلبات %</label>
                                                                <input type="number" name="custom_percentage_rate"
                                                                    value="{{ $pricing->custom_percentage_rate }}"
                                                                    class="form-input" step="0.01" min="0"
                                                                    max="100" placeholder="اتركه فارغاً للافتراضي">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm mb-1">مبلغ ثابت/طلب</label>
                                                                <input type="number" name="custom_fixed_order_fee"
                                                                    value="{{ $pricing->custom_fixed_order_fee }}"
                                                                    class="form-input" step="0.01" min="0"
                                                                    placeholder="اتركه فارغاً للافتراضي">
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <label class="block text-sm mb-1">ملاحظات</label>
                                                            <textarea name="notes" rows="2" class="form-textarea w-full">{{ $pricing->notes }}</textarea>
                                                        </div>
                                                        <div class="mt-4">
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="is_active" value="1"
                                                                    class="form-checkbox"
                                                                    {{ $pricing->is_active ? 'checked' : '' }}>
                                                                <span class="mr-2">نشط</span>
                                                            </label>
                                                        </div>
                                                        <div class="mt-5 flex justify-end gap-2">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                @click="open = false">إلغاء</button>
                                                            <button type="submit" class="btn btn-primary">حفظ</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">لا توجد أسعار خاصة حالياً</p>
                                    <p class="text-sm">جميع الشركات تستخدم الأسعار الافتراضية</p>
                                </td>
                            </tr>
                        @endforelse

                        @if ($companyPrices->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">لا توجد أسعار خاصة حالياً</p>
                                    <p class="text-sm">جميع الشركات تستخدم الأسعار الافتراضية</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal إضافة سعر خاص جديد -->
    <div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'add-company-price') open = true"
        x-on:close-modal.window="open = false" x-show="open" class="fixed inset-0 z-[999] overflow-y-auto"
        style="display: none;">
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="fixed inset-0 bg-black/60"></div>
            <div class="panel relative w-full max-w-lg rounded-lg border-0 p-0 overflow-hidden">
                <div class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3 dark:bg-[#121c2c]">
                    <h5 class="text-lg font-bold">إضافة سعر خاص لشركة</h5>
                </div>
                <div class="p-5" x-data="{ selectedCompany: '' }">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">اختر الشركة <span
                                class="text-danger">*</span></label>
                        <select class="form-select" x-model="selectedCompany" required>
                            <option value="">-- اختر شركة --</option>
                            @foreach ($companies as $company)
                                @if (!isset($companyPrices[$company->code]))
                                    <option value="{{ $company->code }}">{{ $company->name }} ({{ $company->code }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <form :action="'{{ url('subscriptions/company-pricing') }}/' + selectedCompany" method="POST"
                        x-show="selectedCompany" x-cloak>
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">سعر المستخدم الواحد في الشهر</label>
                                <input type="number" name="price_per_user_monthly" class="form-input" step="0.01"
                                    min="0" placeholder="اتركه فارغاً للافتراضي">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">نسبة الطلبات %</label>
                                <input type="number" name="custom_percentage_rate" class="form-input" step="0.01"
                                    min="0" max="100" placeholder="اتركه فارغاً للافتراضي">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">مبلغ ثابت/طلب</label>
                                <input type="number" name="custom_fixed_order_fee" class="form-input" step="0.01"
                                    min="0" placeholder="اتركه فارغاً للافتراضي">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" class="form-textarea w-full" placeholder="سبب السعر الخاص..."></textarea>
                        </div>
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="form-checkbox" checked>
                                <span class="mr-2">نشط</span>
                            </label>
                        </div>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" @click="open = false">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>

                    <div x-show="!selectedCompany" class="text-center py-4 text-gray-500">
                        <p>اختر شركة لتحديد السعر الخاص بها</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
