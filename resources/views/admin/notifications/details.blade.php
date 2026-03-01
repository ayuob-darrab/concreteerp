@extends('layouts.app')

@section('page-title', 'تفاصيل الإشعار')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.notifications.list') }}"
                        class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">تفاصيل الإشعار</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">معلومات شاملة عن الإشعار والشركات المستلمة
                        </p>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الإشعار -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div
                    class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">إجمالي المرسل</p>
                            <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2">{{ $stats['total_sent'] }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">شركة</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">قرأوا الإشعار</p>
                            <p class="text-3xl font-bold text-green-900 dark:text-white mt-2">{{ $stats['read_count'] }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ $stats['total_sent'] > 0 ? round(($stats['read_count'] / $stats['total_sent']) * 100) : 0 }}%
                                من الإجمالي
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-green-200 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">لم يقرأوا بعد</p>
                            <p class="text-3xl font-bold text-yellow-900 dark:text-white mt-2">{{ $stats['unread_count'] }}
                            </p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                {{ $stats['total_sent'] > 0 ? round(($stats['unread_count'] / $stats['total_sent']) * 100) : 0 }}%
                                من الإجمالي
                            </p>
                        </div>
                        <div
                            class="w-14 h-14 bg-yellow-200 dark:bg-yellow-800 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى الإشعار -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    محتوى الإشعار
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">العنوان</label>
                        <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $notification->title }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الرسالة</label>
                        <p class="text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">{{ $notification->message }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">النوع</label>
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $notification->type === 'info'
                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                    : ($notification->type === 'warning'
                                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                        : ($notification->type === 'success'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300')) }}">
                                @if ($notification->type === 'info')
                                    ℹ️ معلومات
                                @elseif($notification->type === 'warning')
                                    ⚠️ تحذير
                                @elseif($notification->type === 'success')
                                    ✅ نجاح
                                @else
                                    🔴 طارئ
                                @endif
                            </span>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الإرسال</label>
                            <p class="text-gray-900 dark:text-white mt-1">
                                {{ $notification->created_at->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول الشركات المستلمة -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        الشركات المستلمة
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    الشركة
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    تاريخ القراءة
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($relatedNotifications as $notif)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <span class="text-xl">🏢</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $notif->company->name ?? 'شركة محذوفة' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $notif->company_code }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($notif->is_read)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                مقروء
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                جديد
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($notif->is_read)
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $notif->read_at->format('Y-m-d') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $notif->read_at->format('h:i A') }}</div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">لم يُقرأ بعد</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
