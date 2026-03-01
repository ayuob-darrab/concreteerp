@extends('layouts.app')

@section('page-title', 'الطلبات وأوامر العمل - كل الأفرع')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold dark:text-white-light mb-1">الطلبات وأوامر العمل لكل الأفرع</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">متابعة الطلبات الجديدة، قيد العمل، والمكتملة حسب الفرع</p>
    </div>

    @php
        $totalNew = collect($branchesData)->sum('newCount');
        $totalActive = collect($branchesData)->sum('activeCount');
        $totalCompleted = collect($branchesData)->sum('completedCount');
    @endphp

    {{-- ملخص عام --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="panel p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold">{{ count($branchesData) }}</div>
                    <p class="text-xs text-gray-500">الأفرع النشطة</p>
                </div>
            </div>
        </div>
        <div class="panel p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-amber-500">{{ $totalNew }}</div>
                    <p class="text-xs text-gray-500">جديدة</p>
                </div>
            </div>
        </div>
        <div class="panel p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-info">{{ $totalActive }}</div>
                    <p class="text-xs text-gray-500">قيد العمل</p>
                </div>
            </div>
        </div>
        <div class="panel p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-success">{{ $totalCompleted }}</div>
                    <p class="text-xs text-gray-500">مكتملة</p>
                </div>
            </div>
        </div>
    </div>

    @forelse ($branchesData as $data)
        @php $branch = $data->branch; @endphp
        <div class="panel mb-6 overflow-hidden">
            {{-- رأس الفرع --}}
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-primary/5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-primary dark:text-white-light flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ $branch->branch_name }}
                </h2>
                <div class="flex flex-wrap gap-2">
                    <span class="badge bg-amber-500/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-full text-xs">
                        جديدة: {{ $data->newCount }}
                    </span>
                    <span class="badge bg-info/20 text-info px-3 py-1 rounded-full text-xs">
                        قيد العمل: {{ $data->activeCount }}
                    </span>
                    <span class="badge bg-success/20 text-success px-3 py-1 rounded-full text-xs">
                        مكتملة: {{ $data->completedCount }}
                    </span>
                </div>
            </div>

            @if ($data->newCount == 0 && $data->activeCount == 0 && $data->completedCount == 0)
                <div class="p-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p>لا توجد أوامر عمل لهذا الفرع</p>
                </div>
            @else
                <div class="p-4">
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

                        {{-- الطلبات الجديدة --}}
                        <div class="rounded-lg border border-amber-200 dark:border-amber-800/50 overflow-hidden">
                            <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50">
                                <h3 class="font-semibold flex items-center gap-2 text-amber-700 dark:text-amber-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    جديدة
                                    <span class="badge bg-amber-500/20 text-amber-600 text-xs mr-auto">{{ $data->newCount }}</span>
                                </h3>
                            </div>
                            <div class="p-3">
                                @if ($data->newJobs->count() > 0)
                                    <ul class="space-y-2 max-h-72 overflow-y-auto">
                                        @foreach ($data->newJobs as $job)
                                            <li class="flex justify-between items-center p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 transition-colors">
                                                <div>
                                                    <span class="font-mono text-sm font-semibold text-primary">{{ $job->job_number }}</span>
                                                    <span class="text-xs text-gray-500 block mt-0.5">{{ $job->customer_name ?? '-' }}</span>
                                                </div>
                                                <div class="text-left">
                                                    <span class="text-sm font-bold">{{ $job->total_quantity }} م&#179;</span>
                                                    <span class="text-xs text-gray-400 block">{{ number_format($job->final_price ?? 0, 0) }} د.ع</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-400 text-sm py-6 text-center">لا توجد طلبات جديدة</p>
                                @endif
                            </div>
                        </div>

                        {{-- قيد العمل --}}
                        <div class="rounded-lg border border-blue-200 dark:border-blue-800/50 overflow-hidden">
                            <div class="px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800/50">
                                <h3 class="font-semibold flex items-center gap-2 text-blue-700 dark:text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    قيد العمل
                                    <span class="badge bg-info/20 text-info text-xs mr-auto">{{ $data->activeCount }}</span>
                                </h3>
                            </div>
                            <div class="p-3">
                                @if ($data->activeJobs->count() > 0)
                                    <ul class="space-y-2 max-h-72 overflow-y-auto">
                                        @foreach ($data->activeJobs as $job)
                                            @php
                                                $totalQty = (float) ($job->total_quantity ?? 0);
                                                $executedQty = (float) ($job->executed_quantity ?? 0);
                                                $jobProgress = $totalQty > 0 ? min(100, round(($executedQty / $totalQty) * 100, 1)) : 0;
                                            @endphp
                                            <li class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="font-mono text-sm font-semibold text-primary">{{ $job->job_number }}</span>
                                                    <span class="font-bold text-info">{{ number_format($jobProgress, 1) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-2 overflow-hidden">
                                                    <div class="h-2 rounded-full bg-info transition-all" style="width: {{ $jobProgress }}%;"></div>
                                                </div>
                                                <div class="flex justify-between text-xs text-gray-500">
                                                    <span>{{ $job->customer_name ?? '-' }}</span>
                                                    <span>{{ number_format($executedQty, 1) }} / {{ number_format($totalQty, 1) }} م&#179;</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-400 text-sm py-6 text-center">لا توجد أوامر قيد العمل</p>
                                @endif
                            </div>
                        </div>

                        {{-- المكتملة --}}
                        <div class="rounded-lg border border-green-200 dark:border-green-800/50 overflow-hidden">
                            <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800/50">
                                <h3 class="font-semibold flex items-center gap-2 text-green-700 dark:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    مكتملة
                                    <span class="badge bg-success/20 text-success text-xs mr-auto">{{ $data->completedCount }}</span>
                                </h3>
                            </div>
                            <div class="p-3">
                                @if ($data->completedJobs->count() > 0)
                                    <ul class="space-y-2 max-h-72 overflow-y-auto">
                                        @foreach ($data->completedJobs as $job)
                                            <li class="flex justify-between items-center p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 transition-colors">
                                                <div>
                                                    <span class="font-mono text-sm font-semibold text-primary">{{ $job->job_number }}</span>
                                                    <span class="text-xs text-gray-500 block mt-0.5">
                                                        {{ $job->actual_end_date ? \Carbon\Carbon::parse($job->actual_end_date)->format('Y-m-d') : '-' }}
                                                    </span>
                                                </div>
                                                <span class="text-sm font-bold text-success">{{ $job->total_quantity }} م&#179;</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($data->completedCount > 10)
                                        <p class="text-xs text-gray-400 mt-2 text-center">عرض آخر 10 من {{ $data->completedCount }}</p>
                                    @endif
                                @else
                                    <p class="text-gray-400 text-sm py-6 text-center">لا توجد أوامر مكتملة</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="panel text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-500">لا توجد أفرع نشطة</h3>
            <p class="text-gray-400 mt-2">لم يتم إضافة أفرع لهذه الشركة بعد</p>
        </div>
    @endforelse
@endsection
