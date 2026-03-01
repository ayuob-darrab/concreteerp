@extends('layouts.app')

@section('page-title', 'تخصيص الآليات')

@section('content')
    <div class="panel mt-6">
        {{-- العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-xl font-bold dark:text-white-light">
                    <span class="text-2xl">🚛</span> تخصيص الآليات والسائقين
                </h3>
                <p class="text-gray-500 mt-1">
                    أمر العمل: <span class="font-semibold text-primary">{{ $job->job_number }}</span>
                </p>
            </div>
            <a href="{{ url('companyBranch/workJob/' . $job->id . '/view') }}" class="btn btn-outline-secondary">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع
            </a>
        </div>

        {{-- معلومات أمر العمل --}}
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-gray-500 text-sm block">العميل</span>
                    <span class="font-semibold">{{ $job->customer_name }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">نوع الخرسانة</span>
                    <span class="font-semibold">{{ $job->concreteType->classification ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">الكمية المطلوبة</span>
                    <span class="font-semibold text-lg text-primary">{{ $job->total_quantity }} م³</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">تاريخ التنفيذ</span>
                    <span
                        class="font-semibold">{{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('Y-m-d') : '-' }}</span>
                </div>
            </div>
        </div>

        <form action="{{ url('companyBranch/workJob/' . $job->id . '/saveAssignment') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- الخلاطات --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                    <h4 class="text-lg font-semibold mb-4">
                        <span class="text-xl">🚛</span> اختيار الخلاطات
                    </h4>

                    @if ($mixers->count() > 0)
                        <div class="space-y-3">
                            @foreach ($mixers as $mixer)
                                <label
                                    class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                    <input type="checkbox" name="mixers[]" value="{{ $mixer->id }}"
                                        class="form-checkbox text-primary rounded">
                                    <div class="mr-3 flex-1">
                                        <div class="font-semibold">{{ $mixer->car_number }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $mixer->car_model }}
                                            @if ($mixer->carType)
                                                - {{ $mixer->carType->name }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($mixer->driver_name)
                                        <span class="badge bg-info/20 text-info text-xs">{{ $mixer->driver_name }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <p>لا توجد خلاطات متاحة</p>
                        </div>
                    @endif
                </div>

                {{-- السائقين --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                    <h4 class="text-lg font-semibold mb-4">
                        <span class="text-xl">👷</span> اختيار السائقين
                    </h4>

                    @if ($drivers->count() > 0)
                        <div class="space-y-3">
                            @foreach ($drivers as $driver)
                                <label
                                    class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                    <input type="checkbox" name="drivers[]" value="{{ $driver->id }}"
                                        class="form-checkbox text-primary rounded">
                                    <div class="mr-3 flex-1">
                                        <div class="font-semibold">{{ $driver->fullname }}</div>
                                        <div class="text-sm text-gray-500">{{ $driver->phone }}</div>
                                    </div>
                                    @if ($driver->employeeType)
                                        <span class="badge bg-secondary text-xs">{{ $driver->employeeType->name }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <p>لا يوجد سائقين متاحين</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- المشرف --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mt-6">
                <h4 class="text-lg font-semibold mb-4">
                    <span class="text-xl">👔</span> المشرف على العمل
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">اختر المشرف</label>
                        <select name="supervisor_id" class="form-select w-full">
                            <option value="">-- اختياري --</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}"
                                    {{ $job->supervisor_id == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ملاحظات التخصيص</label>
                        <input type="text" name="assignment_notes" class="form-input w-full"
                            placeholder="ملاحظات إضافية...">
                    </div>
                </div>
            </div>

            {{-- أزرار الإجراءات --}}
            <div class="flex justify-center gap-4 mt-6">
                <button type="submit" class="btn btn-primary px-8">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ التخصيص
                </button>
                <button type="submit" name="start_work" value="1" class="btn btn-success px-8">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    </svg>
                    حفظ وبدء العمل
                </button>
            </div>
        </form>
    </div>
@endsection
