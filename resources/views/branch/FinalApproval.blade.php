@extends('layouts.app')

@section('page-title', 'الموافقة النهائية وتحويل للعمل')

@section('content')
    <style>
        body.dark .final-approval-page .panel { background-color: #1f2937 !important; border: 1px solid #374151; }
        body.dark .final-approval-page .panel h2,
        body.dark .final-approval-page .panel h3,
        body.dark .final-approval-page .panel .font-medium { color: #e5e7eb !important; }
        body.dark .final-approval-page .panel .text-gray-500 { color: #9ca3af !important; }
        body:not(.dark) .final-approval-page .panel { color: #1f2937; }
        body:not(.dark) .final-approval-page .panel h2,
        body:not(.dark) .final-approval-page .panel h3 { color: #1f2937 !important; }
        .final-approval-page .price-panel { background: linear-gradient(to right, #10b981, #059669) !important; color: #fff !important; }
        body.dark .final-approval-page .price-panel { background: linear-gradient(to right, #047857, #065f46) !important; color: #fff !important; }
        .final-approval-page .price-panel * { color: inherit !important; }
    </style>
    <div class="final-approval-page max-w-5xl mx-auto">
        <!-- رأس الصفحة -->
        <div class="panel mb-5 bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="text-3xl">🎯</span>
                        الموافقة النهائية على الطلب
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">طلب رقم: #{{ $WorkOrder->id }}</p>
                </div>
                <a href="{{ url('companyBranch/listApprovedByContractor') }}" class="btn btn-outline-secondary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- حالة الموافقات -->
        <div
            class="panel mb-5 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700">
            <h3 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-4 flex items-center gap-2">
                <span>✅</span> حالة الموافقات
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- موافقة الفرع على السعر -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-r-4 border-green-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-green-500 text-xl">✓</span>
                        <span class="font-medium text-gray-800 dark:text-gray-300">موافقة الفرع</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">تم اقتراح السعر</p>
                    <p class="text-green-600 dark:text-green-400 font-bold mt-1">{{ number_format($WorkOrder->price ?? 0, 0) }} د.ع</p>
                </div>

                <!-- موافقة المقاول -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-r-4 border-green-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-green-500 text-xl">✓</span>
                        <span class="font-medium text-gray-800 dark:text-gray-300">موافقة المقاول</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $WorkOrder->requester_approval_date ? \Carbon\Carbon::parse($WorkOrder->requester_approval_date)->format('Y-m-d H:i') : '-' }}
                    </p>
                    @if ($WorkOrder->requester_approval_note)
                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">"{{ $WorkOrder->requester_approval_note }}"</p>
                    @endif
                </div>

                <!-- الموافقة النهائية -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-r-4 border-yellow-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-yellow-500 text-xl">⏳</span>
                        <span class="font-medium text-gray-800 dark:text-gray-300">الموافقة النهائية</span>
                    </div>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">بانتظار موافقتك</p>
                </div>
            </div>
        </div>

        <!-- تفاصيل الطلب -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
            <!-- معلومات المقاول -->
            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-900 dark:text-white">
                    <span>👷</span> معلومات المقاول
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الاسم:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $WorkOrder->sender->fullname ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الهاتف:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200" dir="ltr">{{ $WorkOrder->sender->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- معلومات الطلب -->
            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-900 dark:text-white">
                    <span>📋</span> تفاصيل الطلب
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">نوع الخلطة:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $WorkOrder->ConcreteMix->classification ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الكمية:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $WorkOrder->quantity }} م³</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الموقع:</span>
                        <div class="text-left">
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $WorkOrder->location ?? '-' }}</span>
                            @if ($WorkOrder->location_map_url)
                                <a href="{{ $WorkOrder->location_map_url }}" target="_blank"
                                    class="inline-flex items-center gap-1 text-primary hover:text-primary/80 text-sm mr-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    خرائط
                                </a>
                            @endif
                        </div>
                    </div>
                    @if ($WorkOrder->note)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600 dark:text-gray-400">ملاحظات:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $WorkOrder->note }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- السعر النهائي (السعر المتفق عليه) -->
        <div class="panel mb-5 price-panel bg-gradient-to-r from-emerald-500 to-green-600 text-white">
            <div class="text-center py-4">
                <p class="text-white/90 text-lg mb-2">السعر المتفق عليه</p>
                <p class="text-5xl font-bold text-white">{{ number_format($WorkOrder->price ?? 0, 0) }}</p>
                <p class="text-white/90 text-xl mt-2">دينار عراقي</p>
            </div>
        </div>

        <!-- نموذج الموافقة النهائية -->
        <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-900 dark:text-white">
                <span>📝</span> الموافقة النهائية وتحويل للعمل
            </h3>

            <form action="{{ url('companyBranch/' . $WorkOrder->id) }}" method="POST" id="finalApprovalForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="active" value="FinalApproval">

                <!-- تاريخ التنفيذ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="execution_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            تاريخ التنفيذ المتوقع <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="execution_date" id="execution_date" class="form-input w-full"
                            value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label for="execution_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            وقت التنفيذ المتوقع
                        </label>
                        <input type="time" name="execution_time" id="execution_time" class="form-input w-full"
                            value="08:00">
                    </div>
                </div>

                <!-- ملاحظات الموافقة -->
                <div class="mb-6">
                    <label for="final_approval_note"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        ملاحظات الموافقة النهائية (اختياري)
                    </label>
                    <textarea name="final_approval_note" id="final_approval_note" rows="3" class="form-textarea w-full"
                        placeholder="أي ملاحظات إضافية للعمل..."></textarea>
                </div>

                <!-- أزرار الإجراء -->
                <div class="flex flex-wrap gap-4 justify-center border-t border-gray-200 dark:border-gray-700 pt-6">
                    <button type="submit" class="btn btn-success btn-lg gap-2 px-8"
                        onclick="return confirm('هل أنت متأكد من الموافقة النهائية وتحويل الطلب للعمل؟')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        موافقة نهائية وتحويل للعمل
                    </button>

                    <a href="{{ url('companyBranch/listApprovedByContractor') }}"
                        class="btn btn-outline-secondary btn-lg gap-2 px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
