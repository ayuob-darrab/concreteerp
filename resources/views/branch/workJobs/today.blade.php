@extends('layouts.app')

@section('page-title', 'أعمال اليوم 📅')

@section('content')
    <div class="panel mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">
                <span class="text-2xl">📅</span> أعمال اليوم - {{ now()->format('Y-m-d') }}
            </h3>
            <div class="flex items-center gap-2">
                <span class="badge bg-primary/20 text-primary px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $jobs->count() }} أمر عمل
                </span>
            </div>
        </div>

        @if ($jobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($jobs as $job)
                    <div
                        class="panel border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                        {{-- الرأس --}}
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">{{ $job->job_number }}</h4>
                                <p class="text-sm text-gray-500">{{ $job->customer_name }}</p>
                            </div>
                            <span class="badge bg-{{ $job->status_badge }} px-3 py-1">
                                {{ $job->status_label }}
                            </span>
                        </div>

                        {{-- التفاصيل --}}
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">نوع الكونكريت:</span>
                                <span class="font-medium">{{ $job->concreteType->classification ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">الكمية:</span>
                                <span class="font-medium">{{ $job->total_quantity }} م³</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">المنفذ:</span>
                                <span class="font-medium text-success">{{ $job->executed_quantity }} م³</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">الموعد:</span>
                                <span
                                    class="font-medium">{{ $job->scheduled_time ? \Carbon\Carbon::parse($job->scheduled_time)->format('H:i') : 'غير محدد' }}</span>
                            </div>
                        </div>

                        {{-- شريط التقدم --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs mb-1">
                                <span>نسبة الإنجاز</span>
                                <span>{{ number_format($job->completion_percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ $job->completion_percentage }}%">
                                </div>
                            </div>
                        </div>

                        {{-- الإجراءات --}}
                        <div class="mt-4 flex gap-2">
                            <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/view"
                                class="btn btn-sm btn-outline-primary flex-1">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                عرض
                            </a>
                            @if ($job->status == 'pending' || $job->status == 'materials_reserved')
                                <form action="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/start" method="POST"
                                    class="flex-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-full">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        </svg>
                                        بدء
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 text-lg">لا توجد أعمال مجدولة لليوم</p>
                <p class="text-gray-400 text-sm mt-2">ستظهر الأعمال المجدولة هنا</p>
            </div>
        @endif
    </div>
@endsection
