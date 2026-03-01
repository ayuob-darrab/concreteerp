@extends('layouts.app')

@section('page-title', 'تذاكر الدعم')

@section('content')
    <div class="panel">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">🎫 تذاكر الدعم الفني</h2>
                <p class="text-sm text-gray-500 mt-1">إرسال استفسارات ومشاكل للدعم الفني</p>
            </div>
            <a href="{{ route('support.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                تذكرة جديدة
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-100">{{ session('success') }}</div>
        @endif

        {{-- إحصائيات (ألوان ثابتة لظهور النص في الثيم الفاتح والداكن) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
            <div class="rounded-xl p-4 text-center shadow-md" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs mt-1 opacity-95">إجمالي التذاكر</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['open'] }}</div>
                <div class="text-xs mt-1 opacity-95">مفتوحة</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['in_progress'] }}</div>
                <div class="text-xs mt-1 opacity-95">قيد المعالجة</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['pending'] ?? 0 }}</div>
                <div class="text-xs mt-1 opacity-95">بانتظار الرد</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['resolved'] }}</div>
                <div class="text-xs mt-1 opacity-95">محلولة</div>
            </div>
        </div>

        {{-- فلاتر --}}
        <div
            class="flex flex-wrap items-center gap-3 mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الحالة:</label>
                <select id="filterStatus" onchange="applyFilters()" class="form-select form-select-sm w-auto min-w-[120px]">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة
                    </option>
                    <option value="pending_response" {{ request('status') == 'pending_response' ? 'selected' : '' }}>
                        بانتظار الرد</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>محلولة</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الأولوية:</label>
                <select id="filterPriority" onchange="applyFilters()"
                    class="form-select form-select-sm w-auto min-w-[100px]">
                    <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                </select>
            </div>
            <div class="flex items-center gap-2 flex-1">
                <input type="text" id="searchInput" placeholder="بحث برقم التذكرة أو العنوان..."
                    value="{{ request('search') }}" class="form-input form-input-sm w-full max-w-xs"
                    onkeypress="if(event.key==='Enter') applyFilters()">
                <button onclick="applyFilters()" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- جدول التذاكر --}}
        <div class="table-responsive">
            <table class="table-hover">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="text-center w-28 text-gray-700 dark:text-gray-200">رقم التذكرة</th>
                        <th class="text-gray-700 dark:text-gray-200">الموضوع</th>
                        <th class="text-center w-24 text-gray-700 dark:text-gray-200">التصنيف</th>
                        <th class="text-center w-24 text-gray-700 dark:text-gray-200">الأولوية</th>
                        <th class="text-center w-28 text-gray-700 dark:text-gray-200">الحالة</th>
                        <th class="text-center w-20 text-gray-700 dark:text-gray-200">الردود</th>
                        <th class="text-center w-28 text-gray-700 dark:text-gray-200">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $priorityBorder = [
                                'urgent' => 'border-r-4 border-red-500',
                                'high' => 'border-r-4 border-orange-400',
                                'medium' => '',
                                'low' => '',
                            ];
                            $statusBg = [
                                'open' => 'bg-blue-50/50 dark:bg-blue-900/10',
                                'pending_response' => 'bg-purple-50/50 dark:bg-purple-900/10',
                                'resolved' => 'bg-green-50/50 dark:bg-green-900/10',
                            ];
                        @endphp
                        <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $priorityBorder[$ticket->priority] ?? '' }} {{ $statusBg[$ticket->status] ?? '' }}"
                            onclick="window.location='{{ route('support.show', $ticket->id) }}'">
                            <td class="text-center">
                                <span
                                    class="font-mono text-sm text-primary font-medium">{{ $ticket->ticket_number }}</span>
                            </td>
                            <td>
                                <div class="font-medium text-gray-800 dark:text-white">{{ $ticket->subject }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ Str::limit($ticket->description, 60) }}</div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                    {{ $ticket->category_name }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $priorityColors = [
                                        'low' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'medium' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'high' =>
                                            'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $priorityColors[$ticket->priority] ?? '' }}">
                                    {{ $ticket->priority_name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-outline-{{ $ticket->status_color }}">
                                    {{ $ticket->status_name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                        </path>
                                    </svg>
                                    {{ $ticket->replies_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-sm text-gray-700 dark:text-gray-300">{{ $ticket->created_at->format('Y/m/d') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-3 opacity-50 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">لا توجد تذاكر</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ابدأ بإنشاء تذكرة جديدة للتواصل مع الدعم الفني
                                    </p>
                                    <a href="{{ route('support.create') }}" class="btn btn-primary btn-sm mt-4">
                                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        إنشاء تذكرة جديدة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($tickets->hasPages())
            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <script>
        function applyFilters() {
            const status = document.getElementById('filterStatus').value;
            const priority = document.getElementById('filterPriority').value;
            const search = document.getElementById('searchInput').value;
            const url = new URL(window.location.href);

            url.searchParams.set('status', status);
            url.searchParams.set('priority', priority);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            window.location.href = url.toString();
        }
    </script>
@endsection
