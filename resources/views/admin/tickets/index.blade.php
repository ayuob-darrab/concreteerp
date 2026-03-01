@extends('layouts.app')

@section('page-title', 'تذاكر الدعم')

@section('content')
    <div class="panel">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">🎫 إدارة تذاكر الدعم الفني</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">جميع طلبات الدعم من الشركات</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        {{-- إحصائيات - تنسيق مضمن لظهورها في الثيم الفاتح والداكن --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="rounded-xl p-4 text-center shadow-md border border-slate-400/30" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">الإجمالي</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md border border-blue-400/30" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['open'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">مفتوحة</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md border border-amber-400/30" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['in_progress'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">قيد المعالجة</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md border border-purple-400/30" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['pending'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">بانتظار الرد</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md border border-green-400/30" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['resolved'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">محلولة</div>
            </div>
            <div class="rounded-xl p-4 text-center shadow-md border border-gray-400/30" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: #fff;">
                <div class="text-3xl font-bold">{{ $stats['closed'] }}</div>
                <div class="text-xs mt-1" style="color: rgba(255,255,255,0.9);">مغلقة</div>
            </div>
        </div>

        {{-- فلاتر --}}
        <div
            class="flex flex-wrap items-center gap-3 mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">الحالة:</label>
                <select id="filterStatus" onchange="applyFilters()" class="form-select form-select-sm w-auto min-w-[120px] bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm">
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
                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">الأولوية:</label>
                <select id="filterPriority" onchange="applyFilters()"
                    class="form-select form-select-sm w-auto min-w-[100px] bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm">
                    <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">الشركة:</label>
                <select id="filterCompany" onchange="applyFilters()"
                    class="form-select form-select-sm w-auto min-w-[140px] bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm">
                    <option value="all">كل الشركات</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->code }}" {{ request('company') == $company->code ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 flex-1">
                <input type="text" id="searchInput" placeholder="بحث برقم التذكرة أو العنوان..."
                    value="{{ request('search') }}" class="form-input form-input-sm w-full max-w-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm"
                    onkeypress="if(event.key==='Enter') applyFilters()">
                <button type="button" onclick="applyFilters()"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-white text-sm font-medium bg-blue-600 hover:bg-blue-700 transition-colors"
                    style="background-color:#2563eb;color:#fff;">
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
                    <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                        <th class="text-center w-28 py-3 px-2">رقم التذكرة</th>
                        <th class="w-40 py-3 px-2">الشركة</th>
                        <th class="py-3 px-2">الموضوع</th>
                        <th class="text-center w-24 py-3 px-2">التصنيف</th>
                        <th class="text-center w-24 py-3 px-2">الأولوية</th>
                        <th class="text-center w-28 py-3 px-2">الحالة</th>
                        <th class="text-center w-20 py-3 px-2">الردود</th>
                        <th class="text-center w-28 py-3 px-2">التاريخ</th>
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
                            onclick="window.location='{{ route('admin.tickets.show', $ticket->id) }}'">
                            <td class="text-center">
                                <span class="font-mono text-sm font-medium text-blue-600 dark:text-blue-400">{{ $ticket->ticket_number }}</span>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $ticket->company->name ?? 'غير محدد' }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $ticket->user->fullname ?? '' }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ $ticket->subject }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                    {{ Str::limit($ticket->description, 50) }}</div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
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
                                    {{ $ticket->replies_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $ticket->created_at->format('Y/m/d') }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="text-gray-600 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300 font-medium">لا توجد تذاكر</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">نظام التذاكر جاهز لاستقبال طلبات الدعم</p>
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
            const company = document.getElementById('filterCompany').value;
            const search = document.getElementById('searchInput').value;
            const url = new URL(window.location.href);

            url.searchParams.set('status', status);
            url.searchParams.set('priority', priority);
            url.searchParams.set('company', company);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            window.location.href = url.toString();
        }
    </script>
@endsection
