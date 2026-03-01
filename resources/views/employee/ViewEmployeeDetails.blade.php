


@extends('layouts.app')

@section('page-title', 'عرض معلومات الموظف: ' . $employee->fullname)

@section('content')

    <div class="max-w-6xl mx-auto">
        <!-- العنوان الرئيسي -->
        <div class="panel mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($employee->personImage && file_exists(public_path($employee->personImage)))
                        <img src="{{ asset($employee->personImage) }}" alt="صورة الموظف"
                            class="w-16 h-16 rounded-full object-cover shadow-lg border-2 border-primary">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-bold dark:text-white-light">{{ $employee->fullname }}</h3>
                        <p class="text-sm text-gray-500">{{ $employee->employeeType->name ?? 'موظف' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="badge {{ $employee->isactive ? 'bg-success' : 'bg-danger' }} text-white px-3 py-1 rounded-full">
                        {{ $employee->isactive ? 'مفعل' : 'معطل' }}
                    </span>
                    <a href="{{ url('Employees/' . $employee->id . '&EditEmployeeDetails/edit') }}"
                        class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- معلومات الاتصال -->
            <div class="panel">
                <div class="flex items-center gap-2 mb-4 border-b pb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                    <h4 class="font-semibold text-lg">معلومات الاتصال</h4>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">رقم الهاتف</p>
                            <p class="font-medium" dir="ltr">{{ $employee->phone ?? 'غير متوفر' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">البريد الإلكتروني</p>
                            <p class="font-medium">{{ $employee->email ?? 'غير متوفر' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات العمل -->
            <div class="panel">
                <div class="flex items-center gap-2 mb-4 border-b pb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <h4 class="font-semibold text-lg">معلومات العمل</h4>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">الفرع</p>
                            <p class="font-medium">{{ $employee->Branchesname->branch_name ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">الشفت</p>
                            <p class="font-medium">{{ $employee->shift->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">تاريخ التعيين</p>
                            <p class="font-medium">{{ $employee->createdate ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المعلومات المالية -->
            <div class="panel">
                <div class="flex items-center gap-2 mb-4 border-b pb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <h4 class="font-semibold text-lg">المعلومات المالية</h4>
                </div>
                <div class="space-y-4">
                    <div
                        class="bg-gradient-to-r from-green-500/10 to-green-500/5 dark:from-green-500/20 dark:to-green-500/10 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">الراتب الشهري</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($employee->salary, 0) }}
                            <span class="text-sm font-normal">IQD</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">نوع الموظف</p>
                            <p class="font-medium">{{ $employee->employeeType->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الصورة الشخصية والملف PDF -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- الصورة الشخصية -->
            <div class="panel">
                <div class="flex items-center gap-2 mb-4 border-b pb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <h4 class="font-semibold text-lg">الصورة الشخصية</h4>
                </div>
                <div class="text-center">
                    @if ($employee->personImage && file_exists(public_path($employee->personImage)))
                        <img src="{{ asset($employee->personImage) }}" alt="صورة الموظف"
                            class="w-48 h-48 rounded-lg mx-auto object-cover shadow-lg border border-gray-200 dark:border-gray-700">
                    @else
                        <div
                            class="w-48 h-48 rounded-lg mx-auto bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-gray-400 text-sm">لا توجد صورة</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ملف PDF -->
            <div class="panel">
                <div class="flex items-center gap-2 mb-4 border-b pb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h4 class="font-semibold text-lg">الملف الشخصي (PDF)</h4>
                </div>
                @if ($employee->file && file_exists(public_path($employee->file)))
                    <iframe src="{{ asset($employee->file) }}" width="100%" height="400px"
                        class="rounded-lg border border-gray-200 dark:border-gray-700 shadow-md"></iframe>
                    <div class="mt-3 text-center">
                        <a href="{{ asset($employee->file) }}" target="_blank"
                            class="btn btn-outline-primary btn-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            تحميل الملف
                        </a>
                    </div>
                @else
                    <div
                        class="h-[400px] rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-400">لا يوجد ملف PDF</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- زر الرجوع -->
        <div class="mt-6 text-center">
            <a href="{{ url('/Employees/ListEmployees') }}" class="btn btn-outline-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة لقائمة الموظفين
            </a>
        </div>
    </div>

@endsection
