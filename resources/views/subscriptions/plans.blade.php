@extends('layouts.app')

@section('page-title', 'خطط الاشتراك')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- البطاقات المختصرة --}}
        <div class="panel h-full w-full">
            <div class="mb-4 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">أنواع الخطط</h5>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700">
                    <h6 class="text-base font-semibold">شهري</h6>
                    <p class="text-sm text-gray-600 dark:text-gray-300">رسوم ثابتة شهرياً، طلبات غير محدودة.</p>
                </div>
                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700">
                    <h6 class="text-base font-semibold">سنوي</h6>
                    <p class="text-sm text-gray-600 dark:text-gray-300">رسوم ثابتة سنوياً بخصم، طلبات غير محدودة.</p>
                </div>
                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700">
                    <h6 class="text-base font-semibold">نسبة من الطلبات</h6>
                    <p class="text-sm text-gray-600 dark:text-gray-300">نسبة مئوية من قيمة كل طلب منفذ.</p>
                </div>
                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700">
                    <h6 class="text-base font-semibold">تجريبي</h6>
                    <p class="text-sm text-gray-600 dark:text-gray-300">100 طلب مجاني، ثم التحويل لخطة مدفوعة.</p>
                </div>
                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700 md:col-span-2">
                    <h6 class="text-base font-semibold">هجين</h6>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        رسوم شهرية أساسية + نسبة من الطلبات التي تتجاوز الحد المتفق عليه.
                    </p>
                </div>
            </div>
        </div>

        {{-- التنبيهات ومراقبة الحدود --}}
        <div class="panel h-full w-full">
            <div class="mb-4 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">التنبيهات وحدود الاستخدام</h5>
            </div>
            <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <li>تنبيه انتهاء الاشتراك: 15، 7، 3 أيام قبل الانتهاء.</li>
                <li>تنبيه تجاوز الحد: عند 90% ثم 100% من حد الطلبات.</li>
                <li>إيقاف الخدمة تلقائياً عند انتهاء الاشتراك وعدم التجديد.</li>
                <li>دعم بوابات الدفع، الدفع المباشر، والتجديد التلقائي.</li>
            </ul>
        </div>
    </div>

    {{-- جدول مختصر للخطط والخصائص --}}
    <div class="mt-6 panel h-full w-full">
        <div class="mb-4 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">الخصائص الرئيسة لكل خطة</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <th class="pb-2">الخطة</th>
                        <th class="pb-2">السعر</th>
                        <th class="pb-2">حد الطلبات</th>
                        <th class="pb-2">طريقة الفوترة</th>
                        <th class="pb-2">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <tr>
                        <td class="py-2 font-semibold">شهري</td>
                        <td class="py-2">سعر ثابت شهري</td>
                        <td class="py-2">غير محدود</td>
                        <td class="py-2">شهري</td>
                        <td class="py-2">خصم عند الدفع السنوي مقدماً</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">سنوي</td>
                        <td class="py-2">سعر ثابت سنوي مخفض</td>
                        <td class="py-2">غير محدود</td>
                        <td class="py-2">سنوي</td>
                        <td class="py-2">الأفضل للشركات المستقرة</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">نسبة من الطلبات</td>
                        <td class="py-2">نسبة مئوية لكل طلب</td>
                        <td class="py-2">غير محدود</td>
                        <td class="py-2">حسب التنفيذ</td>
                        <td class="py-2">مناسب للأحجام المتغيرة</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">تجريبي</td>
                        <td class="py-2">مجاني حتى 100 طلب</td>
                        <td class="py-2">100 طلب</td>
                        <td class="py-2">مرة واحدة</td>
                        <td class="py-2">يحوَّل تلقائياً لخطة مدفوعة بعد الحد</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">هجين</td>
                        <td class="py-2">رسوم أساسية + نسبة بعد حد معين</td>
                        <td class="py-2">غير محدود</td>
                        <td class="py-2">شهري + نسبة</td>
                        <td class="py-2">مرن للشركات سريعة النمو</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

