@extends('layouts.app')

@section('page-title', 'التقارير المالية - مدير الشركة')

@section('content')
    <div class="container-fluid">

        <!-- رأس الصفحة -->
        <div class="panel bg-gradient-to-r from-slate-700 to-slate-900 text-white mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">📊</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">التقارير المالية للمدير</h2>
                        <p class="text-slate-300 mt-1">نظرة شاملة على أداء الشركة والفروع</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="bg-white/10 px-3 py-1 rounded-full">
                        📅 {{ now()->translatedFormat('l، j F Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- الإحصائيات السريعة -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- ملخص الطلبات -->
            <div class="panel border-l-4 border-blue-500">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">📋 ملخص الطلبات</h3>
                        <p class="text-sm text-gray-500">الشهر الحالي</p>
                    </div>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                        {{ $quickStats['orders']['count'] }} طلب
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ number_format($quickStats['orders']['total_value'], 0) }}</div>
                        <div class="text-xs text-gray-500">إجمالي القيمة (د.ع)</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ number_format($quickStats['orders']['total_quantity'], 1) }}</div>
                        <div class="text-xs text-gray-500">الكمية (م³)</div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500">
                        <span class="text-green-500 font-bold">{{ $quickStats['orders']['completed'] }}</span> طلب مكتمل
                    </span>
                    <a href="{{ route('manager-reports.orders') }}" class="btn btn-sm btn-primary">
                        عرض التقرير الكامل →
                    </a>
                </div>
            </div>

            <!-- ملخص الصيانة -->
            <div class="panel border-l-4 border-orange-500">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">🔧 ملخص الصيانة</h3>
                        <p class="text-sm text-gray-500">الشهر الحالي</p>
                    </div>
                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-sm font-bold">
                        {{ $quickStats['maintenance']['count'] }} صيانة
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-3 text-center">
                        <div class="text-xl font-bold text-red-600">
                            {{ number_format($quickStats['maintenance']['total_cost'], 0) }}</div>
                        <div class="text-xs text-gray-500">الإجمالي</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-3 text-center">
                        <div class="text-xl font-bold text-purple-600">
                            {{ number_format($quickStats['maintenance']['parts_cost'], 0) }}</div>
                        <div class="text-xs text-gray-500">القطع</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3 text-center">
                        <div class="text-xl font-bold text-green-600">
                            {{ number_format($quickStats['maintenance']['labor_cost'], 0) }}</div>
                        <div class="text-xs text-gray-500">العمالة</div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500">
                        إجمالي التكاليف بالدينار
                    </span>
                    <a href="{{ route('manager-reports.maintenance') }}" class="btn btn-sm btn-warning">
                        عرض التقرير الكامل →
                    </a>
                </div>
            </div>
        </div>

        <!-- التقارير المتاحة -->
        <h3 class="text-xl font-bold mb-4 text-slate-800 dark:text-slate-200">📑 التقارير المتاحة</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- تقرير الطلبات المالي -->
            <a href="{{ route('manager-reports.orders') }}"
                class="panel hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform">
                        📋
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">التقرير المالي للطلبات</h4>
                        <p class="text-sm text-gray-500">طلبات الكونكريت والمبيعات</p>
                    </div>
                </div>
                <ul class="text-sm text-gray-600 space-y-1 mb-4">
                    <li>✓ إحصائيات مالية شاملة</li>
                    <li>✓ توزيع حسب الفرع والخلطة</li>
                    <li>✓ رسوم بيانية تفاعلية</li>
                    <li>✓ طباعة احترافية</li>
                </ul>
                <div class="flex items-center text-blue-600 font-semibold">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </div>
            </a>

            <!-- تقرير الصيانة المالي -->
            <a href="{{ route('manager-reports.maintenance') }}"
                class="panel hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform">
                        🔧
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">التقرير المالي للصيانة</h4>
                        <p class="text-sm text-gray-500">صيانة سيارات الفرع</p>
                    </div>
                </div>
                <ul class="text-sm text-gray-600 space-y-1 mb-4">
                    <li>✓ تكاليف القطع والعمالة</li>
                    <li>✓ أعلى السيارات تكلفة</li>
                    <li>✓ توزيع أنواع الصيانة</li>
                    <li>✓ طباعة احترافية</li>
                </ul>
                <div class="flex items-center text-orange-600 font-semibold">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </div>
            </a>

            <!-- روابط سريعة -->
            <div class="panel bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                <h4 class="font-bold text-lg mb-4">🔗 روابط سريعة</h4>
                <div class="space-y-2">
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                        <span>📊 جميع التقارير</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <a href="{{ route('car-maintenance.index') }}"
                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                        <span>🚗 إدارة الصيانة</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <a href="{{ route('cash.daily') }}"
                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                        <span>💰 الصندوق اليومي</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- الفروع -->
        @if ($branches->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-4 text-slate-800 dark:text-slate-200">🏢 فروع الشركة</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($branches as $branch)
                        <div class="panel text-center p-4">
                            <div
                                class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xl">🏢</span>
                            </div>
                            <h5 class="font-semibold text-sm">{{ $branch->branch_name }}</h5>
                            <p class="text-xs text-gray-500 mt-1">{{ $branch->city->name ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
