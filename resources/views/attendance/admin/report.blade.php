@extends('layouts.app')

@section('title', 'تقرير الحضور والانصراف')
@section('page-title', 'تقرير الحضور والانصراف')

@section('content')
    {{-- أنماط لظهور البيانات في ثيم مدير الشركة والثيم الفاتح/الداكن --}}
    <style>
        #attendance-report-page .report-panel {
            border-radius: 0.5rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: visible;
        }

        body:not(.dark) #attendance-report-page .report-panel {
            background-color: #ffffff !important;
            color: #1f2937 !important;
            border: 1px solid #e5e7eb;
        }

        body.dark #attendance-report-page .report-panel {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border: 1px solid #374151;
        }

        body:not(.dark) #attendance-report-page .report-panel label,
        body:not(.dark) #attendance-report-page .report-panel h4,
        body:not(.dark) #attendance-report-page .report-panel h5,
        body:not(.dark) #attendance-report-page .report-panel th,
        body:not(.dark) #attendance-report-page .report-panel td,
        body:not(.dark) #attendance-report-page .report-panel .text-gray-500 {
            color: #374151 !important;
        }

        body.dark #attendance-report-page .report-panel label,
        body.dark #attendance-report-page .report-panel h4,
        body.dark #attendance-report-page .report-panel h5,
        body.dark #attendance-report-page .report-panel th,
        body.dark #attendance-report-page .report-panel td {
            color: #e5e7eb !important;
        }

        body:not(.dark) #attendance-report-page .report-panel thead th {
            background-color: #f3f4f6 !important;
        }

        body.dark #attendance-report-page .report-panel thead th {
            background-color: #374151 !important;
        }

        body:not(.dark) #attendance-report-page .report-panel .form-input,
        body:not(.dark) #attendance-report-page .report-panel .form-select {
            background-color: #fff !important;
            color: #1f2937 !important;
            border-color: #d1d5db;
        }
    </style>
    <div id="attendance-report-page" x-data="reportPage()">

        {{-- العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white">📋 تقرير الحضور والانصراف</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @if (isset($branches) && $branches->count() > 0 && !request('branch_id'))
                        عرض سجلات الحضور لـ <strong>جميع الفروع</strong> — يمكنك اختيار فرع محدد من الفلتر أدناه
                    @elseif (isset($branches) && $branches->count() > 0 && request('branch_id'))
                        عرض سجلات الحضور للفرع المحدد
                    @else
                        عرض وتصفية سجلات الحضور حسب الفرع والتاريخ
                    @endif
                </p>
            </div>
            <a href="{{ route('attendance.admin.export', request()->query()) }}"
                class="btn btn-success btn-sm flex items-center gap-2">
                📥 تصدير Excel
            </a>
        </div>

        {{-- الفلاتر --}}
        <div class="panel report-panel mb-5">
            <form method="GET" action="{{ route('attendance.admin.report') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 items-end">

                    {{-- فلتر الفرع --}}
                    @if (isset($branches) && $branches->count() > 0)
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">🏢
                                الفرع</label>
                            <select name="branch_id" class="form-select w-full">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- من تاريخ --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">📅 من
                            تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from', $startDate) }}"
                            class="form-input w-full">
                    </div>

                    {{-- إلى تاريخ --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">📅 إلى
                            تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to', $endDate) }}"
                            class="form-input w-full">
                    </div>

                    {{-- الموظف --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">👤 الموظف</label>
                        <select name="employee_id" class="form-select w-full">
                            <option value="">جميع الموظفين</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->fullname }}{{ $emp->employeeType ? ' — ' . $emp->employeeType->name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- القسم / نوع الموظف --}}
                    @if (isset($employeeTypes) && $employeeTypes->count() > 0)
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">📁
                                القسم</label>
                            <select name="employee_type_id" class="form-select w-full">
                                <option value="">جميع الأقسام</option>
                                @foreach ($employeeTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('employee_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- الحالة --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300">📊 الحالة</label>
                        <select name="status" class="form-select w-full">
                            <option value="">الكل</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>✅ حاضر</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>⏰ متأخر</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>❌ غائب</option>
                            <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>📅 إجازة
                            </option>
                        </select>
                    </div>

                    {{-- أزرار --}}
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary flex-1 flex items-center justify-center gap-2">🔍
                            بحث</button>
                        <a href="{{ route('attendance.admin.report') }}" class="btn btn-outline-secondary px-3"
                            title="إعادة تعيين">↻</a>
                    </div>

                </div>
            </form>
        </div>

        {{-- بطاقات الملخص --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">📋</div>
                    <div>
                        <div class="text-xl font-bold text-primary">{{ $summary['total_records'] ?? 0 }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">إجمالي السجلات</p>
                    </div>
                </div>
            </div>
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center shrink-0">✅</div>
                    <div>
                        <div class="text-xl font-bold text-success">{{ $summary['present'] ?? 0 }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">حاضر</p>
                    </div>
                </div>
            </div>
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">⏰</div>
                    <div>
                        <div class="text-xl font-bold text-warning">{{ $summary['late'] ?? 0 }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">متأخر</p>
                    </div>
                </div>
            </div>
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-danger/10 flex items-center justify-center shrink-0">❌</div>
                    <div>
                        <div class="text-xl font-bold text-danger">{{ $summary['absent'] ?? 0 }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">غائب</p>
                    </div>
                </div>
            </div>
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center shrink-0">📅</div>
                    <div>
                        <div class="text-xl font-bold text-info">{{ $summary['on_leave'] ?? 0 }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">إجازة</p>
                    </div>
                </div>
            </div>
            <div class="panel report-panel p-4" style="color: inherit;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center shrink-0">
                        🕐
                    </div>
                    <div>
                        <div class="text-xl font-bold text-secondary">{{ $summary['total_late_hours'] ?? '0:00' }}</div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">ساعات التأخير</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="panel report-panel">
            <h5 class="text-lg font-semibold dark:text-white-light mb-4 flex items-center gap-2">
                📊 سجلات الحضور
                <span class="text-sm font-normal text-gray-500">
                    ({{ $startDate }} إلى {{ $endDate }})
                </span>
            </h5>

            <div class="table-responsive">
                <table class="table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center w-10">#</th>
                            <th>الموظف</th>
                            <th class="text-center">القسم</th>
                            @if (isset($branches) && $branches->count() > 0 && !request('branch_id'))
                                <th class="text-center">الفرع</th>
                            @endif
                            <th class="text-center">التاريخ</th>
                            <th class="text-center">اليوم</th>
                            <th class="text-center">الحضور</th>
                            <th class="text-center">الانصراف</th>
                            <th class="text-center">التأخير</th>
                            <th class="text-center">ساعات العمل</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center w-16">تعديل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td class="text-center text-gray-400 text-sm">{{ $attendances->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if ($attendance->employee && $attendance->employee->personImage)
                                            <img src="{{ asset($attendance->employee->personImage) }}"
                                                class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600"
                                                alt="">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                                                <span
                                                    class="text-primary text-xs font-bold">{{ mb_substr($attendance->employee->fullname ?? '?', 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span
                                            class="font-semibold text-gray-800 dark:text-gray-200">{{ $attendance->employee->fullname ?? 'غير معروف' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300">{{ $attendance->employee && $attendance->employee->employeeType ? $attendance->employee->employeeType->name : '—' }}</span>
                                </td>
                                @if (isset($branches) && $branches->count() > 0 && !request('branch_id'))
                                    <td class="text-center text-sm">
                                        <span class="badge bg-info/20 text-info px-2 py-1 rounded text-xs">
                                            {{ $attendance->branch->branch_name ?? '-' }}
                                        </span>
                                    </td>
                                @endif
                                <td class="text-center font-mono text-sm">
                                    {{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                <td class="text-center text-sm">{{ $attendance->attendance_date->translatedFormat('l') }}
                                </td>
                                <td class="text-center">
                                    @if ($attendance->check_in_time)
                                        <span
                                            class="font-mono text-sm text-success font-semibold">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</span>
                                    @else
                                        <span class="text-gray-300">--:--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($attendance->check_out_time)
                                        <span
                                            class="font-mono text-sm text-danger font-semibold">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}</span>
                                    @else
                                        <span class="text-gray-300">--:--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($attendance->late_minutes > 0)
                                        <span
                                            class="badge bg-warning/20 text-warning text-xs">{{ $attendance->late_human }}</span>
                                    @else
                                        <span class="text-success text-sm">&#10003;</span>
                                    @endif
                                </td>
                                <td class="text-center font-mono text-sm">{{ $attendance->total_work_human ?? '-' }}</td>
                                <td class="text-center">
                                    @switch($attendance->status)
                                        @case('present')
                                            <span class="badge bg-success/20 text-success">حاضر</span>
                                        @break

                                        @case('late')
                                            <span class="badge bg-warning/20 text-warning">متأخر</span>
                                        @break

                                        @case('absent')
                                            <span class="badge bg-danger/20 text-danger">غائب</span>
                                        @break

                                        @case('on_leave')
                                        @case('sick_leave')
                                            <span class="badge bg-info/20 text-info">إجازة</span>
                                        @break

                                        @default
                                            <span
                                                class="badge bg-secondary/20 text-secondary">{{ $attendance->status_label }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <button type="button" @click="editAttendance({{ json_encode($attendance) }})"
                                        class="btn btn-sm btn-outline-primary p-1.5" title="تعديل">
                                        ✏️
                                    </button>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    @php $emptyCols = (isset($branches) && $branches->count() > 0 && !request('branch_id')) ? 13 : 12; @endphp
                                    <td colspan="{{ $emptyCols }}" class="text-center py-12">
                                        <div class="text-5xl mb-3">📋</div>
                                        <p class="text-gray-400 text-lg font-semibold">لا توجد سجلات للفترة المحددة</p>
                                        <p class="text-gray-300 dark:text-gray-500 text-sm mt-1">جرب تغيير معايير البحث</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendances->hasPages())
                    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        {{ $attendances->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- مودال التعديل --}}
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black/50" @click="showEditModal = false"></div>
                    <div class="panel max-w-lg w-full relative z-10">
                        <div class="flex items-center justify-between mb-5">
                            <h5 class="text-lg font-semibold flex items-center gap-2">
                                ✏️ تعديل سجل الحضور
                            </h5>
                            <button type="button" @click="showEditModal = false"
                                class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        <form @submit.prevent="submitEdit()">
                            <div class="grid gap-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">وقت الحضور</label>
                                        <input type="time" x-model="editForm.check_in_time" class="form-input w-full">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">وقت الانصراف</label>
                                        <input type="time" x-model="editForm.check_out_time" class="form-input w-full">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">الحالة</label>
                                    <select x-model="editForm.status" class="form-select w-full">
                                        <option value="present">حاضر</option>
                                        <option value="late">متأخر</option>
                                        <option value="absent">غائب</option>
                                        <option value="on_leave">إجازة</option>
                                        <option value="sick_leave">إجازة مرضية</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">سبب التعديل <span
                                            class="text-danger">*</span></label>
                                    <textarea x-model="editForm.modification_reason" class="form-textarea w-full" rows="2" required
                                        placeholder="اذكر سبب التعديل..."></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <button type="button" @click="showEditModal = false"
                                    class="btn btn-outline-danger">إلغاء</button>
                                <button type="submit" class="btn btn-primary" :disabled="submitting">
                                    <span x-show="!submitting">حفظ التعديلات</span>
                                    <span x-show="submitting">جاري الحفظ...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const baseUrl = '{{ url('/') }}';
            document.addEventListener('alpine:init', () => {
                Alpine.data('reportPage', () => ({
                    showEditModal: false,
                    submitting: false,
                    editForm: {
                        id: null,
                        check_in_time: '',
                        check_out_time: '',
                        status: '',
                        modification_reason: ''
                    },

                    editAttendance(attendance) {
                        this.editForm = {
                            id: attendance.id,
                            check_in_time: attendance.check_in_time ? attendance.check_in_time
                                .substring(0, 5) : '',
                            check_out_time: attendance.check_out_time ? attendance
                                .check_out_time.substring(0, 5) : '',
                            status: attendance.status,
                            modification_reason: ''
                        };
                        this.showEditModal = true;
                    },

                    async submitEdit() {
                        if (!this.editForm.modification_reason) {
                            Swal.fire('خطأ', 'يرجى إدخال سبب التعديل', 'error');
                            return;
                        }

                        this.submitting = true;
                        try {
                            const response = await fetch(
                                `${baseUrl}/attendance/admin/${this.editForm.id}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(this.editForm)
                                });

                            const data = await response.json();

                            if (data.success) {
                                await Swal.fire('تم بنجاح', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('خطأ', data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            Swal.fire('خطأ', 'حدث خطأ في الاتصال بالخادم', 'error');
                        } finally {
                            this.submitting = false;
                        }
                    }
                }));
            });
        </script>
    @endsection
