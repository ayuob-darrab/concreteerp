@extends('layouts.app')

@section('title', 'لوحة تحكم الحضور')

@section('content')
    <div x-data="adminDashboard()">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="/ConcreteERP" class="text-primary hover:underline">الرئيسية</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>لوحة تحكم الحضور</span>
            </li>
        </ul>

        <div class="pt-5">
            {{-- بطاقات الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="panel bg-gradient-to-r from-cyan-500 to-cyan-400">
                    <div class="flex justify-between">
                        <div class="text-white">
                            <div class="text-3xl font-bold">{{ $stats['total_employees'] ?? 0 }}</div>
                            <div class="mt-1">إجمالي الموظفين</div>
                        </div>
                        <div class="text-white text-opacity-50">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel bg-gradient-to-r from-success to-green-400">
                    <div class="flex justify-between">
                        <div class="text-white">
                            <div class="text-3xl font-bold">{{ $stats['present_today'] ?? 0 }}</div>
                            <div class="mt-1">حاضرون اليوم</div>
                        </div>
                        <div class="text-white text-opacity-50">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel bg-gradient-to-r from-warning to-yellow-400">
                    <div class="flex justify-between">
                        <div class="text-white">
                            <div class="text-3xl font-bold">{{ $stats['late_today'] ?? 0 }}</div>
                            <div class="mt-1">متأخرون اليوم</div>
                        </div>
                        <div class="text-white text-opacity-50">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="panel bg-gradient-to-r from-danger to-red-400">
                    <div class="flex justify-between">
                        <div class="text-white">
                            <div class="text-3xl font-bold">{{ $stats['absent_today'] ?? 0 }}</div>
                            <div class="mt-1">غائبون اليوم</div>
                        </div>
                        <div class="text-white text-opacity-50">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                {{-- حضور اليوم --}}
                <div class="panel">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="text-lg font-semibold dark:text-white-light">
                            <svg class="w-6 h-6 inline-block text-success ltr:mr-2 rtl:ml-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            الحاضرون اليوم
                        </h5>
                        <span class="badge bg-success">{{ $todayAttendances->count() }}</span>
                    </div>
                    <div class="table-responsive max-h-96 overflow-y-auto">
                        <table class="table-hover">
                            <thead class="sticky top-0 bg-white dark:bg-dark">
                                <tr>
                                    <th>الموظف</th>
                                    <th>الحضور</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAttendances as $attendance)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                @if ($attendance->employee && $attendance->employee->personImage)
                                                    <img src="{{ asset('uploads/employees/' . $attendance->employee->personImage) }}"
                                                        class="w-8 h-8 rounded-full object-cover"
                                                        alt="{{ $attendance->employee->fullname }}">
                                                @else
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                                                        <span
                                                            class="text-white text-xs">{{ mb_substr($attendance->employee->fullname ?? '?', 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <span>{{ $attendance->employee->fullname ?? 'غير معروف' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : '-' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $attendance->status === 'present' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $attendance->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-400">لا يوجد حضور مسجل اليوم
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- الغائبون اليوم --}}
                <div class="panel">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="text-lg font-semibold dark:text-white-light">
                            <svg class="w-6 h-6 inline-block text-danger ltr:mr-2 rtl:ml-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            الغائبون اليوم
                        </h5>
                        <span class="badge bg-danger">{{ $absentEmployees->count() }}</span>
                    </div>
                    <div class="table-responsive max-h-96 overflow-y-auto">
                        <table class="table-hover">
                            <thead class="sticky top-0 bg-white dark:bg-dark">
                                <tr>
                                    <th>الموظف</th>
                                    <th>القسم</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absentEmployees as $employee)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                @if ($employee->personImage)
                                                    <img src="{{ asset('uploads/employees/' . $employee->personImage) }}"
                                                        class="w-8 h-8 rounded-full object-cover"
                                                        alt="{{ $employee->fullname }}">
                                                @else
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-danger flex items-center justify-center">
                                                        <span
                                                            class="text-white text-xs">{{ mb_substr($employee->fullname, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <span>{{ $employee->fullname }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $employee->employeeType->name ?? '-' }}</td>
                                        <td>
                                            <button type="button" @click="markAbsent({{ $employee->id }})"
                                                class="btn btn-danger btn-sm">
                                                تسجيل غياب
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-400">لا يوجد غائبين 🎉</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- روابط سريعة --}}
            <div class="panel">
                <h5 class="text-lg font-semibold dark:text-white-light mb-5">
                    <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    روابط سريعة
                </h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('attendance.admin.report') }}"
                        class="btn btn-outline-primary flex flex-col items-center py-4">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        تقرير الحضور
                    </a>
                    <a href="{{ route('attendance.admin.report', ['export' => 1]) }}"
                        class="btn btn-outline-success flex flex-col items-center py-4">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        تصدير Excel
                    </a>
                    <a href="{{ route('attendance.index') }}"
                        class="btn btn-outline-info flex flex-col items-center py-4">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4">
                            </path>
                        </svg>
                        تسجيل حضوري
                    </a>
                    <a href="{{ route('employees.index') }}"
                        class="btn btn-outline-warning flex flex-col items-center py-4">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                        إدارة الموظفين
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminDashboard', () => ({
                async markAbsent(employeeId) {
                    const result = await Swal.fire({
                        title: 'تأكيد تسجيل الغياب',
                        text: 'هل تريد تسجيل هذا الموظف كغائب اليوم؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، سجل غياب',
                        cancelButtonText: 'إلغاء'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(
                                '{{ route('attendance.admin.markAbsent') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        employee_id: employeeId
                                    })
                                });

                            const data = await response.json();

                            if (data.success) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'تم بنجاح',
                                    text: data.message,
                                    confirmButtonText: 'حسناً'
                                });
                                location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: data.message,
                                    confirmButtonText: 'حسناً'
                                });
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ في الاتصال بالخادم',
                                confirmButtonText: 'حسناً'
                            });
                        }
                    }
                }
            }));
        });
    </script>
@endsection
