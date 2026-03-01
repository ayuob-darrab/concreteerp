@extends('layouts.app')

@section('page-title', 'إحصائيات النظام')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إحصائيات النظام</h1>
            </div>

            <!-- Companies Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">الشركات</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $stats['companies']['total'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">إجمالي الشركات</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-300">
                            {{ $stats['companies']['active'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">شركات نشطة</div>
                    </div>
                </div>
            </div>

            <!-- Users Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">المستخدمين</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $stats['users']['total'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">إجمالي المستخدمين</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-300">
                            {{ $stats['users']['by_type']['SA'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">سوبر أدمن</div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">
                            {{ $stats['users']['by_type']['CM'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">مدراء شركات</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-300">
                            {{ $stats['users']['by_type']['BM'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">مدراء فروع</div>
                    </div>
                </div>
            </div>

            <!-- Other Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إحصائيات أخرى</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ $stats['branches'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">الفروع</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">{{ $stats['cars'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">السيارات</div>
                    </div>
                    <div class="bg-indigo-50 dark:bg-indigo-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">{{ $stats['employees'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">الموظفين</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
