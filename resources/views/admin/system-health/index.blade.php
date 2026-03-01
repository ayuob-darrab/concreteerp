@extends('layouts.app')

@section('page-title', 'صحة النظام')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">صحة النظام</h1>
            </div>

            <!-- Health Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Database Status -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-r-4 border-{{ $health['database']['class'] == 'success' ? 'green' : 'red' }}-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">قاعدة البيانات</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $health['database']['status'] }}</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full bg-{{ $health['database']['class'] == 'success' ? 'green' : 'red' }}-100 flex items-center justify-center">
                            @if ($health['database']['class'] == 'success')
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Storage Status -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-r-4 border-{{ $health['storage']['class'] }}-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">مساحة التخزين</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $health['storage']['percentage'] }}%</p>
                            <p class="text-xs text-gray-400">{{ $health['storage']['free'] }} متاح من
                                {{ $health['storage']['total'] }}</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full bg-{{ $health['storage']['class'] }}-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-{{ $health['storage']['class'] }}-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- PHP Version -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-r-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إصدار PHP</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $health['php_version'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-r-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">استخدام الذاكرة</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $health['memory_usage'] }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">معلومات النظام</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-600 dark:text-gray-400">إصدار Laravel</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $health['laravel_version'] }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-600 dark:text-gray-400">إصدار PHP</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $health['php_version'] }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-600 dark:text-gray-400">نظام التشغيل</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ PHP_OS }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-600 dark:text-gray-400">وقت الخادم</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ now()->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>

            <!-- Storage Progress Bar -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">مساحة التخزين</h3>
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    <div class="bg-{{ $health['storage']['class'] }}-600 h-4 rounded-full transition-all"
                        style="width: {{ $health['storage']['percentage'] }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>مستخدم:
                        {{ 100 - ((float) str_replace(' GB', '', $health['storage']['free']) / (float) str_replace(' GB', '', $health['storage']['total'])) * 100 }}%</span>
                    <span>متاح: {{ $health['storage']['free'] }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
