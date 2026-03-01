@extends('layouts.app')

@section('page-title', 'تقارير الأداء')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">تقارير الأداء</h1>
            </div>

            <!-- رسائل النجاح والخطأ -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cache Control -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">التخزين المؤقت (Cache)</h3>
                    @if ($performance['cache_enabled'])
                        <span
                            class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            مفعّل
                        </span>
                    @else
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium flex items-center gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            غير مفعّل
                        </span>
                    @endif
                </div>

                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    التخزين المؤقت يجعل موقعك أسرع بكثير. فعّله فقط بعد رفع المشروع للإنترنت (Production).
                </p>

                <!-- Cache Status -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div
                        class="p-3 rounded-lg {{ $performance['cache_status']['config'] ? 'bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                        <div class="flex items-center gap-2">
                            @if ($performance['cache_status']['config'])
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الإعدادات</span>
                        </div>
                    </div>
                    <div
                        class="p-3 rounded-lg {{ $performance['cache_status']['routes'] ? 'bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                        <div class="flex items-center gap-2">
                            @if ($performance['cache_status']['routes'])
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">المسارات</span>
                        </div>
                    </div>
                    <div
                        class="p-3 rounded-lg {{ $performance['cache_status']['views'] ? 'bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                        <div class="flex items-center gap-2">
                            @if ($performance['cache_status']['views'])
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">العروض</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    @if (!$performance['cache_enabled'])
                        <form action="{{ route('admin.cache.enable') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                تفعيل التخزين المؤقت
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.cache.disable') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                إيقاف التخزين المؤقت
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Server Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">معلومات الخادم</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">إصدار PHP</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $performance['server_info']['php_version'] }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">إصدار Laravel</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $performance['server_info']['laravel_version'] }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">حجم قاعدة البيانات</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $performance['database_size'] }}
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">عدد الجداول</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $performance['total_tables'] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Tips -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">نصائح لتحسين الأداء</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                        <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">تفعيل التخزين المؤقت</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">php artisan config:cache && php artisan
                                route:cache</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">تحسين الصور</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">استخدم صور مضغوطة بحجم مناسب</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">مراقبة السجلات</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">راجع سجل الأخطاء بشكل دوري</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
