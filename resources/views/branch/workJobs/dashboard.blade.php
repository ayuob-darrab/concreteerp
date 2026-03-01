@extends('layouts.app')

@section('page-title', 'لوحة تحكم التنفيذ 📊')

@section('content')
    {{-- تنسيق الثيم الفاتح والداكن للوحة التحكم --}}
    <style>
        /* الثيم الفاتح: النص الداكن على البانلات البيضاء */
        body:not(.dark) .execution-dashboard-page .panel { color: #1f2937; }
        body:not(.dark) .execution-dashboard-page .panel h5 { color: #1f2937 !important; }
        body:not(.dark) .execution-dashboard-page .panel .text-center p { color: #4b5563 !important; }
        /* كروت الإحصائيات الأربعة في الثيم الفاتح: إجبار التدرج حتى لا تُستبدل بخلفية بيضاء فيختفي النص */
        body:not(.dark) .execution-dashboard-page .stat-card-cyan { background: linear-gradient(to right, #06b6d4, #22d3ee) !important; color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-cyan .text-white,
        body:not(.dark) .execution-dashboard-page .stat-card-cyan a { color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-violet { background: linear-gradient(to right, #8b5cf6, #a78bfa) !important; color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-violet .text-white,
        body:not(.dark) .execution-dashboard-page .stat-card-violet a { color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-amber { background: linear-gradient(to right, #f59e0b, #fbbf24) !important; color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-amber .text-white,
        body:not(.dark) .execution-dashboard-page .stat-card-amber a { color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-green { background: linear-gradient(to right, #22c55e, #4ade80) !important; color: #fff !important; }
        body:not(.dark) .execution-dashboard-page .stat-card-green .text-white,
        body:not(.dark) .execution-dashboard-page .stat-card-green a { color: #fff !important; }
        /* الثيم الداكن */
        body.dark .execution-dashboard-page .panel { background-color: #1f2937 !important; border: 1px solid #374151; }
        body.dark .execution-dashboard-page .panel h5,
        body.dark .execution-dashboard-page .panel .font-semibold:not(.text-white):not(.text-primary) { color: #e5e7eb !important; }
        body.dark .execution-dashboard-page .panel .text-gray-500 { color: #9ca3af !important; }
        body.dark .execution-dashboard-page .panel a span.font-medium { color: #e5e7eb !important; }
        body.dark .execution-dashboard-page .panel .text-center.text-gray-500 { color: #9ca3af !important; }
        body.dark .execution-dashboard-page .stat-card-cyan { background: linear-gradient(to right, #0e7490, #155e75) !important; }
        body.dark .execution-dashboard-page .stat-card-violet { background: linear-gradient(to right, #5b21b6, #6d28d9) !important; }
        body.dark .execution-dashboard-page .stat-card-amber { background: linear-gradient(to right, #b45309, #d97706) !important; }
        body.dark .execution-dashboard-page .stat-card-green { background: linear-gradient(to right, #15803d, #16a34a) !important; }
    </style>
    <div class="pt-5 execution-dashboard-page">
        {{-- الإحصائيات --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            {{-- أعمال اليوم --}}
            <div class="panel bg-gradient-to-r from-cyan-500 to-cyan-400 stat-card-cyan">
                <div class="flex justify-between">
                    <div class="text-white">
                        <div class="text-5xl font-bold leading-none">{{ $todayJobsCount }}</div>
                        <div class="mt-3 text-lg font-medium">أعمال اليوم</div>
                    </div>
                    <div class="text-white/50 text-7xl">📅</div>
                </div>
                <div class="mt-4">
                    <a href="/ConcreteERP/companyBranch/workJobs/today"
                        class="inline-block text-white text-sm hover:underline">
                        عرض التفاصيل ←
                    </a>
                </div>
            </div>

            {{-- قيد التنفيذ --}}
            <div class="panel bg-gradient-to-r from-violet-500 to-violet-400 stat-card-violet">
                <div class="flex justify-between">
                    <div class="text-white">
                        <div class="text-5xl font-bold leading-none">{{ $activeJobsCount }}</div>
                        <div class="mt-3 text-lg font-medium">قيد التنفيذ</div>
                    </div>
                    <div class="text-white/50 text-7xl">🚧</div>
                </div>
                <div class="mt-4">
                    <a href="/ConcreteERP/companyBranch/workJobs/active"
                        class="inline-block text-white text-sm hover:underline">
                        عرض التفاصيل ←
                    </a>
                </div>
            </div>

            {{-- بانتظار التنفيذ --}}
            <div class="panel bg-gradient-to-r from-amber-500 to-amber-400 stat-card-amber">
                <div class="flex justify-between">
                    <div class="text-white">
                        <div class="text-5xl font-bold leading-none">{{ $pendingJobsCount }}</div>
                        <div class="mt-3 text-lg font-medium">بانتظار التنفيذ</div>
                    </div>
                    <div class="text-white/50 text-7xl">⏳</div>
                </div>
                <div class="mt-4">
                    <a href="/ConcreteERP/companyBranch/workJobs/pending"
                        class="inline-block text-white text-sm hover:underline">
                        عرض التفاصيل ←
                    </a>
                </div>
            </div>

            {{-- مكتمل اليوم --}}
            <div class="panel bg-gradient-to-r from-green-500 to-green-400 stat-card-green">
                <div class="flex justify-between">
                    <div class="text-white">
                        <div class="text-5xl font-bold leading-none">{{ $completedTodayCount }}</div>
                        <div class="mt-3 text-lg font-medium">مكتمل اليوم</div>
                    </div>
                    <div class="text-white/50 text-7xl">✅</div>
                </div>
                <div class="mt-4">
                    <a href="/ConcreteERP/companyBranch/workJobs/completed"
                        class="inline-block text-white text-sm hover:underline">
                        عرض التفاصيل ←
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- الشحنات النشطة --}}
            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg text-gray-900 dark:text-white-light">
                        <span class="text-xl">🚛</span> الشحنات النشطة
                    </h5>
                    <a href="/ConcreteERP/companyBranch/workShipments" class="text-primary hover:text-primary/80 text-sm hover:underline dark:text-primary">
                        عرض الكل
                    </a>
                </div>

                @if ($activeShipments->count() > 0)
                    <div class="space-y-3">
                        @foreach ($activeShipments as $shipment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-warning/20 flex items-center justify-center text-lg animate-pulse">
                                        🚛
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $shipment->shipment_number }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $shipment->mixer->car_number ?? '-' }} -
                                            {{ $shipment->mixerDriver->fullname ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span
                                        class="badge 
                                        @if ($shipment->status == 'departed') bg-warning
                                        @elseif($shipment->status == 'arrived') bg-info
                                        @else bg-primary @endif">
                                        {{ $shipment->status_label }}
                                    </span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $shipment->planned_quantity }} م³
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-600 dark:text-gray-400">
                        <div class="text-4xl mb-2">🚛</div>
                        <p class="text-gray-600 dark:text-gray-400">لا توجد شحنات نشطة حالياً</p>
                    </div>
                @endif
            </div>

            {{-- أعمال اليوم القادمة --}}
            <div class="panel bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg text-gray-900 dark:text-white-light">
                        <span class="text-xl">📋</span> أعمال اليوم القادمة
                    </h5>
                    <a href="/ConcreteERP/companyBranch/workJobs/today" class="text-primary hover:text-primary/80 text-sm hover:underline dark:text-primary">
                        عرض الكل
                    </a>
                </div>

                @if ($todayJobs->count() > 0)
                    <div class="space-y-3">
                        @foreach ($todayJobs as $job)
                            <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/view"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-lg">
                                        📋
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $job->job_number }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $job->customer_name }}</div>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="font-semibold text-primary">{{ $job->total_quantity }} م³</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $job->scheduled_time ? \Carbon\Carbon::parse($job->scheduled_time)->format('H:i') : '-' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-600 dark:text-gray-400">
                        <div class="text-4xl mb-2">📋</div>
                        <p class="text-gray-600 dark:text-gray-400">لا توجد أعمال مجدولة لليوم</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- روابط سريعة --}}
        <div class="panel mt-6 bg-white dark:bg-gray-800 dark:border-gray-700">
            <h5 class="font-semibold text-lg text-gray-900 dark:text-white-light mb-5">
                <span class="text-xl">⚡</span> روابط سريعة
            </h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="/ConcreteERP/companyBranch/workJobs/today"
                    class="flex flex-col items-center p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg hover:shadow-md transition-shadow text-gray-800 dark:text-gray-200">
                    <span class="text-3xl mb-2">📅</span>
                    <span class="font-medium">أعمال اليوم</span>
                </a>
                <a href="/ConcreteERP/companyBranch/workJobs/pending"
                    class="flex flex-col items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:shadow-md transition-shadow text-gray-800 dark:text-gray-200">
                    <span class="text-3xl mb-2">⏳</span>
                    <span class="font-medium">بانتظار التنفيذ</span>
                </a>
                <a href="/ConcreteERP/companyBranch/workJobs/active"
                    class="flex flex-col items-center p-4 bg-violet-50 dark:bg-violet-900/20 rounded-lg hover:shadow-md transition-shadow text-gray-800 dark:text-gray-200">
                    <span class="text-3xl mb-2">🚧</span>
                    <span class="font-medium">قيد التنفيذ</span>
                </a>
                <a href="/ConcreteERP/companyBranch/workShipments"
                    class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:shadow-md transition-shadow text-gray-800 dark:text-gray-200">
                    <span class="text-3xl mb-2">🚛</span>
                    <span class="font-medium">الشحنات</span>
                </a>
            </div>
        </div>
    </div>
@endsection
