@extends('layouts.app')

@section('title', 'تسجيل الحضور')

@section('content')
    <div x-data="attendancePage">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="/ConcreteERP" class="text-primary hover:underline">الرئيسية</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>الحضور والانصراف</span>
            </li>
        </ul>

        <div class="pt-5">
            {{-- بطاقة الترحيب ومعلومات الموظف --}}
            <div class="panel mb-5">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        مرحباً {{ $employee->fullname }}
                    </h5>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-5">
                    <div class="flex items-center gap-4">
                        @if ($employee->personImage && file_exists(public_path($employee->personImage)))
                            <img src="{{ asset($employee->personImage) }}"
                                class="w-20 h-20 rounded-full object-cover border-4 border-primary shadow-lg"
                                alt="{{ $employee->fullname }}">
                        @else
                            <div
                                class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white shadow-lg">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ $employee->fullname }}</h4>
                            <span class="badge bg-info text-sm">{{ $employee->employeeType->name ?? 'موظف' }}</span>
                        </div>
                    </div>
                    <div class="md:ltr:ml-auto md:rtl:mr-auto text-center md:text-left rtl:md:text-right">
                        <p class="text-gray-600 dark:text-gray-400 mb-1">
                            <svg class="w-4 h-4 inline-block ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <strong>الفرع:</strong> {{ $employee->Branchesname->name ?? 'غير محدد' }}
                        </p>
                        @if ($shift)
                            <p class="text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 inline-block ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <strong>الشفت:</strong> {{ $shift->name }}
                                ({{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }})
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- بطاقة تسجيل الحضور --}}
            <div class="panel mb-5">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4">
                            </path>
                        </svg>
                        تسجيل الحضور - {{ \Carbon\Carbon::now()->translatedFormat('l، d/m/Y') }}
                    </h5>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- الساعة الحالية --}}
                    <div class="text-center">
                        <div class="mb-6">
                            <div id="current-time" class="text-6xl font-bold text-primary mb-2">00:00:00</div>
                            <p class="text-gray-500 dark:text-gray-400">الوقت الحالي</p>
                        </div>

                        @if ($shift)
                            <div class="flex justify-center gap-8">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-success">
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                    </div>
                                    <small class="text-gray-500">وقت البداية</small>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-danger">
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                    </div>
                                    <small class="text-gray-500">وقت النهاية</small>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- حالة الحضور اليوم --}}
                    <div class="flex items-center justify-center">
                        @if ($todayAttendance)
                            <div
                                class="w-full max-w-sm rounded-lg p-6 text-center
                                @if ($todayAttendance->status === 'present') bg-success-light
                                @elseif($todayAttendance->status === 'late') bg-warning-light
                                @else bg-info-light @endif">

                                <div class="mb-4">
                                    @if ($todayAttendance->status === 'present')
                                        <svg class="w-16 h-16 mx-auto text-success" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($todayAttendance->status === 'late')
                                        <svg class="w-16 h-16 mx-auto text-warning" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-16 h-16 mx-auto text-info" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <h5
                                    class="text-xl font-bold mb-3
                                    @if ($todayAttendance->status === 'present') text-success
                                    @elseif($todayAttendance->status === 'late') text-warning
                                    @else text-info @endif">
                                    {{ $todayAttendance->status_label }}
                                </h5>

                                <p class="text-gray-600 dark:text-gray-400 mb-2">
                                    <strong>وقت الحضور:</strong>
                                    {{ $todayAttendance->check_in_time ? \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('h:i A') : '-' }}
                                </p>

                                @if ($todayAttendance->late_minutes > 0)
                                    <p class="text-warning mb-2">
                                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        تأخير: {{ $todayAttendance->late_human }}
                                    </p>
                                @endif

                                @if ($todayAttendance->check_out_time)
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                                        <strong>وقت الانصراف:</strong>
                                        {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('h:i A') }}
                                    </p>
                                    <p class="text-success font-semibold">
                                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        إجمالي العمل: {{ $todayAttendance->total_work_human }}
                                    </p>
                                @else
                                    <hr class="my-4 border-gray-200 dark:border-gray-700">
                                    <button type="button" @click="checkOut()" class="btn btn-danger btn-lg w-full"
                                        :disabled="loading">
                                        <template x-if="!loading">
                                            <span>
                                                <svg class="w-5 h-5 inline-block ltr:mr-2 rtl:ml-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                تسجيل الانصراف
                                            </span>
                                        </template>
                                        <template x-if="loading">
                                            <span>
                                                <svg class="animate-spin h-5 w-5 inline-block" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                جاري...
                                            </span>
                                        </template>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="text-center">
                                <div class="mb-6">
                                    <svg class="w-24 h-24 mx-auto text-gray-300 dark:text-gray-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 text-lg">لم تقم بتسجيل حضورك اليوم بعد</p>
                                <button type="button" @click="checkIn()" class="btn btn-success btn-lg px-8"
                                    :disabled="loading">
                                    <template x-if="!loading">
                                        <span>
                                            <svg class="w-5 h-5 inline-block ltr:mr-2 rtl:ml-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            تسجيل الحضور
                                        </span>
                                    </template>
                                    <template x-if="loading">
                                        <span>
                                            <svg class="animate-spin h-5 w-5 inline-block" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            جاري التسجيل...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- إحصائيات الشهر --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="panel text-center py-6">
                    <div class="text-4xl font-bold text-primary mb-2">{{ $monthStats['present_days'] }}</div>
                    <p class="text-gray-500 dark:text-gray-400">أيام الحضور</p>
                </div>
                <div class="panel text-center py-6">
                    <div class="text-4xl font-bold text-warning mb-2">{{ $monthStats['late_days'] }}</div>
                    <p class="text-gray-500 dark:text-gray-400">أيام التأخير</p>
                </div>
                <div class="panel text-center py-6">
                    <div class="text-4xl font-bold text-danger mb-2">{{ $monthStats['absent_days'] }}</div>
                    <p class="text-gray-500 dark:text-gray-400">أيام الغياب</p>
                </div>
                <div class="panel text-center py-6">
                    <div class="text-4xl font-bold text-info mb-2">{{ $monthStats['leave_days'] }}</div>
                    <p class="text-gray-500 dark:text-gray-400">أيام الإجازة</p>
                </div>
            </div>

            {{-- سجل الحضور الأخير --}}
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="text-lg font-semibold dark:text-white-light">
                        <svg class="w-6 h-6 inline-block text-primary ltr:mr-2 rtl:ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        سجل الحضور الأخير
                    </h5>
                    <a href="{{ route('attendance.myHistory') }}" class="btn btn-outline-primary btn-sm">
                        عرض الكل
                        <svg class="w-4 h-4 inline-block ltr:ml-1 rtl:mr-1 rtl:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>اليوم</th>
                                <th>الحضور</th>
                                <th>الانصراف</th>
                                <th>التأخير</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                    <td>{{ $attendance->attendance_date->translatedFormat('l') }}</td>
                                    <td>
                                        {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : '-' }}
                                    </td>
                                    <td>
                                        {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') : '-' }}
                                    </td>
                                    <td>
                                        @if ($attendance->late_minutes > 0)
                                            <span class="text-warning font-semibold">{{ $attendance->late_human }}</span>
                                        @else
                                            <span class="text-success">✓</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($attendance->status === 'present') bg-success
                                            @elseif($attendance->status === 'late') bg-warning
                                            @elseif($attendance->status === 'absent') bg-danger
                                            @else bg-info @endif">
                                            {{ $attendance->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <div class="text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <p>لا يوجد سجل حضور سابق</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('attendancePage', () => ({
                loading: false,
                userLocation: null,

                init() {
                    // تحديث الساعة
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // محاولة الحصول على الموقع
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.userLocation = {
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude
                                };
                            },
                            (error) => {
                                console.log('تعذر الحصول على الموقع:', error);
                            }
                        );
                    }
                },

                updateClock() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const clockEl = document.getElementById('current-time');
                    if (clockEl) {
                        clockEl.textContent = `${hours}:${minutes}:${seconds}`;
                    }
                },

                async checkIn() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('attendance.checkIn') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                notes: '',
                                latitude: this.userLocation?.latitude,
                                longitude: this.userLocation?.longitude
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
                    } finally {
                        this.loading = false;
                    }
                },

                async checkOut() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('attendance.checkOut') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                notes: '',
                                latitude: this.userLocation?.latitude,
                                longitude: this.userLocation?.longitude
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
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>

    <style>
        .bg-success-light {
            background-color: rgba(0, 171, 85, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 171, 0, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(255, 86, 48, 0.1);
        }

        .bg-info-light {
            background-color: rgba(0, 184, 217, 0.1);
        }
    </style>
@endsection
