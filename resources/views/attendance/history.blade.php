@extends('layouts.app')

@section('title', 'سجل الحضور والانصراف')

@section('content')
    <div x-data="historyPage()">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="/ConcreteERP" class="text-primary hover:underline">الرئيسية</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <a href="{{ route('attendance.index') }}" class="text-primary hover:underline">الحضور والانصراف</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>السجل</span>
            </li>
        </ul>

        <div class="pt-5">
            {{-- بطاقة التصفية --}}
            <div class="panel mb-5">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        تصفية البيانات
                    </h5>
                </div>

                <form method="GET" action="{{ route('attendance.myHistory') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="month" class="block text-sm font-medium mb-2">الشهر</label>
                        <select name="month" id="month" class="form-select w-full">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}"
                                    {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium mb-2">السنة</label>
                        <select name="year" id="year" class="form-select w-full">
                            @for ($y = date('Y'); $y >= date('Y') - 2; $y--)
                                <option value="{{ $y }}"
                                    {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2">الحالة</label>
                        <select name="status" id="status" class="form-select w-full">
                            <option value="">الكل</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                            <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>إجازة</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            بحث
                        </button>
                    </div>
                </form>
            </div>

            {{-- إحصائيات الشهر --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-5">
                <div class="panel text-center py-4">
                    <div class="text-3xl font-bold text-primary mb-1">{{ $stats['total_days'] ?? 0 }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">إجمالي الأيام</p>
                </div>
                <div class="panel text-center py-4">
                    <div class="text-3xl font-bold text-success mb-1">{{ $stats['present_days'] ?? 0 }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">أيام الحضور</p>
                </div>
                <div class="panel text-center py-4">
                    <div class="text-3xl font-bold text-warning mb-1">{{ $stats['late_days'] ?? 0 }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">أيام التأخير</p>
                </div>
                <div class="panel text-center py-4">
                    <div class="text-3xl font-bold text-danger mb-1">{{ $stats['absent_days'] ?? 0 }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">أيام الغياب</p>
                </div>
                <div class="panel text-center py-4">
                    <div class="text-3xl font-bold text-info mb-1">{{ $stats['leave_days'] ?? 0 }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">أيام الإجازة</p>
                </div>
            </div>

            {{-- جدول السجل --}}
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        سجل الحضور -
                        {{ \Carbon\Carbon::create(request('year', date('Y')), request('month', date('n')))->translatedFormat('F Y') }}
                    </h5>
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary btn-sm">
                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1 rtl:rotate-180" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        العودة
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th>التاريخ</th>
                                <th>اليوم</th>
                                <th>وقت الحضور</th>
                                <th>وقت الانصراف</th>
                                <th>التأخير</th>
                                <th>ساعات العمل</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $index => $attendance)
                                <tr>
                                    <td>{{ $attendances->firstItem() + $index }}</td>
                                    <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                    <td>{{ $attendance->attendance_date->translatedFormat('l') }}</td>
                                    <td>
                                        @if ($attendance->check_in_time)
                                            <span class="text-success font-semibold">
                                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->check_out_time)
                                            <span class="text-danger font-semibold">
                                                {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->late_minutes > 0)
                                            <span class="text-warning">
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $attendance->late_human }}
                                            </span>
                                        @else
                                            <span class="text-success">
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->total_work_minutes)
                                            {{ $attendance->total_work_human }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($attendance->status === 'present') bg-success
                                            @elseif($attendance->status === 'late') bg-warning
                                            @elseif($attendance->status === 'absent') bg-danger
                                            @elseif($attendance->status === 'on_leave') bg-info
                                            @elseif($attendance->status === 'sick_leave') bg-secondary
                                            @else bg-dark @endif">
                                            {{ $attendance->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($attendance->notes)
                                            <span class="cursor-pointer" x-tooltip="{{ $attendance->notes }}">
                                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                                    </path>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <p class="text-lg">لا يوجد سجل حضور للفترة المحددة</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendances->hasPages())
                    <div class="mt-5">
                        {{ $attendances->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('historyPage', () => ({
                // يمكن إضافة المزيد من التفاعلية هنا
            }));
        });
    </script>
@endsection
