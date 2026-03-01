@extends('layouts.app')

@section('content')
    <div class="panel">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">🔔 إشعارات النظام</h2>
                <p class="text-sm text-gray-500 mt-1">جميع الإشعارات والتنبيهات من إدارة النظام</p>
            </div>
            <button type="button" onclick="markAllAsRead()" class="btn btn-outline-primary btn-sm">
                <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                تحديد الكل كمقروء
            </button>
        </div>

        {{-- إحصائيات (ألوان ثابتة لظهور النص في الثيم الفاتح والداكن) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="rounded-lg p-4 shadow-md flex items-center justify-between" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                <div>
                    <p class="text-sm font-medium opacity-95">إجمالي الإشعارات</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-full p-3" style="background: rgba(255,255,255,0.25);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
            </div>

            <div class="rounded-lg p-4 shadow-md flex items-center justify-between" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">
                <div>
                    <p class="text-sm font-medium opacity-95">إشعارات جديدة</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['new'] }}</p>
                </div>
                <div class="rounded-full p-3" style="background: rgba(255,255,255,0.25);">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                        <circle cx="12" cy="12" r="8"></circle>
                    </svg>
                </div>
            </div>

            <div class="rounded-lg p-4 shadow-md flex items-center justify-between" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff;">
                <div>
                    <p class="text-sm font-medium opacity-95">تم قراءتها</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['read'] }}</p>
                </div>
                <div class="rounded-full p-3" style="background: rgba(255,255,255,0.25);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- فلاتر --}}
        <div class="flex flex-wrap gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الحالة:</label>
                <select id="filterStatus" onchange="applyFilters()" class="form-select form-select-sm w-auto">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">النوع:</label>
                <select id="filterType" onchange="applyFilters()" class="form-select form-select-sm w-auto">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>معلومات</option>
                    <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>تحذير</option>
                    <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>نجاح</option>
                    <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>طارئ</option>
                </select>
            </div>
        </div>

        {{-- قائمة الإشعارات --}}
        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="notification-item border rounded-lg p-4 cursor-pointer transition-all hover:shadow-md
                    {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}"
                    onclick="showNotification({{ $notification->id }})">
                    <div class="flex items-start gap-4">
                        {{-- أيقونة النوع --}}
                        <div class="flex-shrink-0">
                            @switch($notification->type)
                                @case('info')
                                    <div
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @break

                                @case('warning')
                                    <div
                                        class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                    </div>
                                @break

                                @case('success')
                                    <div
                                        class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @break

                                @case('danger')
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @break
                            @endswitch
                        </div>

                        {{-- المحتوى --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if (!$notification->is_read)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">
                                        جديد
                                    </span>
                                @endif
                                <h4 class="font-semibold text-gray-800 dark:text-white truncate">
                                    {{ $notification->title }}
                                </h4>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ Str::limit($notification->message, 120) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <svg class="w-3 h-3 inline-block ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- سهم --}}
                        <div class="flex-shrink-0 text-gray-500 dark:text-gray-400">
                            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400 mt-4 font-medium">لا توجد إشعارات</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>

        {{-- Modal عرض الإشعار (ثابت أعلى الشاشة مع تركيز التركيز) --}}
        <div id="notificationModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 overflow-y-auto"
            style="background: rgba(0,0,0,0.5); top: 0; left: 0; right: 0; bottom: 0;"
            role="dialog" aria-modal="true" aria-labelledby="modalTitle"
            onclick="if (event.target === this) closeModal();">
            <div id="modalDialog" tabindex="-1" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg my-8 relative focus:outline-none"
                onclick="event.stopPropagation();"
                style="max-height: calc(100vh - 4rem);">
                <div class="flex items-center p-4 border-b dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div id="modalIcon" class="w-10 h-10 rounded-full flex items-center justify-center"></div>
                        <h3 id="modalTitle" class="font-semibold text-lg text-gray-800 dark:text-white"></h3>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto">
                    <p id="modalDate" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>
                    <div id="modalMessage" class="text-gray-700 dark:text-gray-300 whitespace-pre-line"></div>
                </div>
                <div class="flex justify-end p-4 border-t dark:border-gray-700">
                    <button id="modalCloseBtn" onclick="closeAndMarkRead()" class="btn btn-primary" type="button">
                        تم القراءة
                    </button>
                </div>
            </div>
        </div>

        <script>
            let currentNotificationId = null;

            function showNotification(id) {
                currentNotificationId = id;

                fetch(`/ConcreteERP/company/notifications/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const n = data.notification;

                            // تحديث Modal
                            document.getElementById('modalTitle').textContent = n.title;
                            document.getElementById('modalMessage').textContent = n.message;
                            document.getElementById('modalDate').textContent = new Date(n.created_at).toLocaleString(
                                'ar-SA');

                            // تحديث الأيقونة
                            const iconDiv = document.getElementById('modalIcon');
                            const icons = {
                                'info': {
                                    bg: 'bg-blue-100',
                                    color: 'text-blue-600',
                                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                },
                                'warning': {
                                    bg: 'bg-amber-100',
                                    color: 'text-amber-600',
                                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>'
                                },
                                'success': {
                                    bg: 'bg-green-100',
                                    color: 'text-green-600',
                                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                },
                                'danger': {
                                    bg: 'bg-red-100',
                                    color: 'text-red-600',
                                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                }
                            };

                            const iconConfig = icons[n.type] || icons['info'];
                            iconDiv.className = `w-10 h-10 ${iconConfig.bg} rounded-full flex items-center justify-center`;
                            iconDiv.innerHTML =
                                `<svg class="w-5 h-5 ${iconConfig.color}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconConfig.icon}</svg>`;

                            // إظهار Modal في أعلى الشاشة والتركيز عليه
                            const modal = document.getElementById('notificationModal');
                            const modalDialog = document.getElementById('modalDialog');
                            const modalBtn = document.getElementById('modalCloseBtn');
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            window.scrollTo(0, 0);
                            modalDialog.focus();
                            setTimeout(function() { modalBtn.focus(); }, 100);
                        }
                    });
            }

            function closeModal() {
                const modal = document.getElementById('notificationModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function closeAndMarkRead() {
                if (currentNotificationId) {
                    fetch(`/ConcreteERP/company/notifications/${currentNotificationId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        location.reload();
                    });
                }
                closeModal();
            }

            function markAllAsRead() {
                fetch('/ConcreteERP/company/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    location.reload();
                });
            }

            function applyFilters() {
                const status = document.getElementById('filterStatus').value;
                const type = document.getElementById('filterType').value;
                const url = new URL(window.location.href);

                url.searchParams.set('status', status);
                url.searchParams.set('type', type);

                window.location.href = url.toString();
            }
        </script>
    @endsection
