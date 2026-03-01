@extends('layouts.app')

@section('page-title', 'مراقبة الاشتراكات')

@section('content')
    <div class="space-y-6">
        <!-- العنوان الرئيسي -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📊 مراقبة الاشتراكات</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">متابعة حالة اشتراكات جميع الشركات</p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                آخر تحديث: {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="panel relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_active'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">اشتراك نشط</div>
                    </div>
                </div>
            </div>
            <div class="panel relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['total_expired'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">منتهي</div>
                    </div>
                </div>
            </div>
            <div class="panel relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-yellow-500"></div>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['total_suspended'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">موقوف</div>
                    </div>
                </div>
            </div>
            <div class="panel relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-orange-500"></div>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['expiring_soon'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">ينتهي خلال 7 أيام</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الاشتراكات -->
        <div class="panel">
            <div class="mb-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h5 class="text-lg font-semibold dark:text-white-light flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    جميع الاشتراكات
                </h5>
                <div class="text-sm text-gray-500">
                    إجمالي: <span class="font-semibold text-primary">{{ $subscriptions->count() }}</span> اشتراك
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr class="!bg-gray-100 dark:!bg-gray-900">
                            <th class="!py-4">الشركة</th>
                            <th class="!py-4">الخطة</th>
                            <th class="!py-4">تاريخ البداية</th>
                            <th class="!py-4">تاريخ النهاية</th>
                            <th class="!py-4">الأيام المتبقية</th>
                            <th class="!py-4">الحالة</th>
                            <th class="!py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="!py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span
                                                class="text-primary font-bold text-sm">{{ mb_substr($subscription->company->name ?? $subscription->company_code, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800 dark:text-white">
                                                {{ $subscription->company->name ?? $subscription->company_code }}</div>
                                            <div class="text-xs text-gray-500">{{ $subscription->company_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="!py-4">
                                    @php
                                        $planNames = [
                                            'basic' => 'أساسي',
                                            'standard' => 'قياسي',
                                            'premium' => 'متميز',
                                            'enterprise' => 'مؤسسي',
                                            'monthly' => 'شهري',
                                            'yearly' => 'سنوي',
                                            'trial' => 'تجريبي',
                                        ];
                                    @endphp
                                    <span
                                        class="font-medium">{{ $planNames[$subscription->plan_type] ?? $subscription->plan_type }}</span>
                                </td>
                                <td class="!py-4 text-gray-600 dark:text-gray-400">
                                    {{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : '-' }}</td>
                                <td class="!py-4 text-gray-600 dark:text-gray-400">
                                    {{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : 'غير محدد' }}
                                </td>
                                <td class="!py-4">
                                    @if (!$subscription->end_date)
                                        <span class="text-info font-semibold text-sm">♾️ على الطلبات</span>
                                    @elseif ($subscription->days_remaining > 30)
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full" style="width: 100%"></div>
                                            </div>
                                            <span
                                                class="text-green-600 font-semibold text-sm">{{ $subscription->days_remaining }}
                                                يوم</span>
                                        </div>
                                    @elseif ($subscription->days_remaining > 7)
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full"
                                                    style="width: {{ min(100, ($subscription->days_remaining / 30) * 100) }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="text-green-600 font-semibold text-sm">{{ $subscription->days_remaining }}
                                                يوم</span>
                                        </div>
                                    @elseif($subscription->days_remaining > 0)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-orange-500 rounded-full"
                                                    style="width: {{ ($subscription->days_remaining / 7) * 100 }}%"></div>
                                            </div>
                                            <span
                                                class="text-orange-600 font-semibold text-sm">{{ $subscription->days_remaining }}
                                                يوم</span>
                                        </div>
                                    @elseif($subscription->days_remaining == 0)
                                        <span class="text-red-600 font-semibold text-sm animate-pulse">⚠️ ينتهي
                                            اليوم</span>
                                    @else
                                        <span class="text-red-600 font-semibold text-sm">❌ منتهي منذ
                                            {{ abs($subscription->days_remaining) }} يوم</span>
                                    @endif
                                </td>
                                <td class="!py-4">
                                    @if ($subscription->status == 'active')
                                        <span class="text-green-500 font-semibold">● نشط</span>
                                    @elseif($subscription->status == 'expired')
                                        <span class="text-red-500 font-semibold">● منتهي</span>
                                    @elseif($subscription->status == 'suspended')
                                        <span class="text-yellow-500 font-semibold">● موقوف</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">● ملغي</span>
                                    @endif
                                </td>
                                <td class="!py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('subscriptions.invoice', $subscription->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 rounded-lg transition-colors"
                                            title="عرض الفاتورة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            الفاتورة
                                        </a>
                                        @php
                                            $companyName = $subscription->company->name ?? $subscription->company_code;
                                            $daysMsg =
                                                $subscription->days_remaining > 0
                                                    ? "باقي {$subscription->days_remaining} يوم على انتهاء الاشتراك"
                                                    : 'انتهى الاشتراك منذ ' .
                                                        abs($subscription->days_remaining) .
                                                        ' يوم';
                                            $whatsappMsg = "مرحباً شركة {$companyName} 🏢\n\nمعكم إدارة النظام\n\n⚠️ تنبيه: {$daysMsg}\n\nيرجى الانتباه والتواصل معنا لتجديد الاشتراك.\n\nشكراً لكم 🙏";
                                            $phone = $subscription->company->phone ?? '';
                                            // تنظيف رقم الهاتف
                                            $phone = preg_replace('/[^0-9]/', '', $phone);
                                            if (substr($phone, 0, 1) === '0') {
                                                $phone = '964' . substr($phone, 1); // العراق
                                            }
                                        @endphp
                                        @if ($phone)
                                            <a href="https://wa.me/{{ $phone }}?text={{ urlencode($whatsappMsg) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-green-50 text-green-600 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 rounded-lg transition-colors"
                                                title="إرسال تنبيه واتساب">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                                </svg>
                                                واتساب
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs" title="لا يوجد رقم هاتف">📵</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="text-gray-500">لا توجد اشتراكات</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- تنبيهات ومعلومات -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="panel">
                <div class="mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h5 class="text-lg font-semibold dark:text-white-light">تنبيهات الاشتراك</h5>
                </div>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg">
                        <span class="w-2 h-2 mt-2 bg-yellow-500 rounded-full flex-shrink-0"></span>
                        <div>
                            <div class="font-medium text-yellow-800 dark:text-yellow-400">تنبيه انتهاء</div>
                            <div class="text-sm text-yellow-600 dark:text-yellow-500">15، 7، 3 أيام قبل نهاية الاشتراك
                            </div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-orange-50 dark:bg-orange-900/10 rounded-lg">
                        <span class="w-2 h-2 mt-2 bg-orange-500 rounded-full flex-shrink-0"></span>
                        <div>
                            <div class="font-medium text-orange-800 dark:text-orange-400">تجاوز حد الطلبات</div>
                            <div class="text-sm text-orange-600 dark:text-orange-500">تنبيه عند 90% ثم 100%</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                        <span class="w-2 h-2 mt-2 bg-red-500 rounded-full flex-shrink-0"></span>
                        <div>
                            <div class="font-medium text-red-800 dark:text-red-400">إيقاف تلقائي</div>
                            <div class="text-sm text-red-600 dark:text-red-500">عند انتهاء الاشتراك بدون تجديد</div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="panel">
                <div class="mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h5 class="text-lg font-semibold dark:text-white-light">تتبع الاستخدام</h5>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        يمكن ربط هذا القسم لاحقاً ببيانات الطلبات المنفذة لحساب نسبة الاستخدام لكل شركة وخطة.
                    </p>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">الميزات القادمة:</span>
                        </div>
                        <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <li>• تقارير استخدام تفصيلية</li>
                            <li>• رسوم بيانية للأداء</li>
                            <li>• تنبيهات ذكية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
