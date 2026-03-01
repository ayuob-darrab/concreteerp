@extends('layouts.app')

@section('page-title', 'إدارة الإشعارات')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <!-- Header with Add Button -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white flex items-center gap-2">
                        <span class="text-2xl">📢</span>
                        إدارة الإشعارات
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">عرض وإدارة جميع الإشعارات المرسلة للشركات</p>
                </div>
                <a href="{{ route('admin.notifications') }}" class="btn btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="font-bold">إرسال إشعار جديد</span>
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div
                    class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">إجمالي المرسل</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-white mt-1">{{ $stats['total_sent'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
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
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">المقروءة</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-white mt-1">{{ $stats['total_read'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
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
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">غير المقروءة</p>
                            <p class="text-2xl font-bold text-yellow-900 dark:text-white mt-1">{{ $stats['total_unread'] }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-yellow-200 dark:bg-yellow-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 dark:text-purple-400">إجمالي الشركات</p>
                            <p class="text-2xl font-bold text-purple-900 dark:text-white mt-1">
                                {{ $stats['companies_count'] }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-purple-200 dark:bg-purple-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.notifications.list') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-3 mb-6">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            نوع الإشعار
                        </label>
                        <select name="type"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                            <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>ℹ️ معلومات</option>
                            <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>⚠️ تحذير</option>
                            <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>✅ نجاح</option>
                            <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>🔴 طارئ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            حالة القراءة
                        </label>
                        <select name="status"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>الكل</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>✅ مقروءة</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>❌ غير مقروءة
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            من تاريخ
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            إلى تاريخ
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            تطبيق
                        </button>
                        <a href="{{ route('admin.notifications.list') }}" class="btn btn-secondary inline-flex items-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Notifications Table -->
            <div class="notifications-table-wrapper rounded-xl overflow-hidden border shadow-sm">
                @if ($notifications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="notifications-table min-w-full">
                            <thead>
                                <tr>
                                    <th>العنوان / النوع</th>
                                    <th>الشركة</th>
                                    <th class="text-center">الحالة</th>
                                    <th>تاريخ القراءة</th>
                                    <th>تاريخ الإرسال</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications as $notification)
                                    <tr>
                                        <td>
                                            <div class="table-cell-content">
                                                <p class="table-title">{{ $notification->title }}</p>
                                                <span class="badge badge-{{ $notification->type }}">
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
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="table-company-icon {{ $notification->company_code === 'ALL' ? 'icon-all' : 'icon-company' }}">
                                                    {{ $notification->company_code === 'ALL' ? '🌐' : '🏢' }}
                                                </span>
                                                <div>
                                                    @if ($notification->company_code === 'ALL')
                                                        <p class="table-company-name table-company-all">جميع الشركات</p>
                                                        <p class="table-company-meta">إشعار عام</p>
                                                    @else
                                                        <p class="table-company-name">{{ $notification->company->name ?? 'غير معروف' }}</p>
                                                        <p class="table-company-meta">{{ $notification->company_code }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($notification->is_read)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">✓ مقروء</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">جديد</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($notification->is_read)
                                                <span class="table-date">{{ $notification->read_at->format('Y-m-d') }}</span>
                                                <span class="table-time">{{ $notification->read_at->format('h:i A') }}</span>
                                            @else
                                                <span class="table-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="table-date">{{ \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d') }}</span>
                                            <span class="table-time">{{ \Carbon\Carbon::parse($notification->created_at)->format('h:i A') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="notifications-table-footer">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">لا توجد إشعارات</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإرسال إشعار جديد للشركات</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-close success messages
        @if (session('success'))
            setTimeout(() => {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        @endif
    </script>

    @if (session('success'))
        <div
            class="alert-success fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div
            class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif
@endsection
