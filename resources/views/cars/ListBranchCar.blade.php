@extends('layouts.app')

@section('page-title', 'سيارات الفرع')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <!-- زر إضافة سيارة جديدة -->
                <a href="/ConcreteERP/cars/addBranchCar" class="btn btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة سيارة جديدة</span>
                </a>
            </div>

            <!-- رسائل النجاح -->
            @if (session('success'))
                <div class="alert alert-success flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- جدول السيارات -->
            <table id="myTable2" class="table-striped whitespace-nowrap w-full">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    السيارات المتوفرة في الفرع
                </caption>
            </table>
        </div>

        <!-- مودال إنهاء تكليف السائقين (يدعم عدة شفتات) -->
        <div x-show="showEndModal" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black/50"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="panel w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg">🚫 إنهاء تكليف السائقين</h5>
                </div>

                <form :action="'/ConcreteERP/cars/' + selectedCarId + '/end-driver-assignment'" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                            <p class="text-gray-600 dark:text-gray-400">
                                السيارة: <span class="font-bold text-primary" x-text="selectedCarNumber"></span>
                            </p>
                        </div>

                        <!-- قائمة الشفتات والسائقين -->
                        <div class="space-y-3">
                            <p class="font-medium text-gray-700 dark:text-gray-300">اختر السائقين المراد إنهاء تكليفهم:</p>

                            <!-- زر تحديد/إلغاء الكل -->
                            <div class="flex gap-2 mb-3">
                                <button type="button" @click="selectAllDrivers()" class="btn btn-sm btn-outline-primary">
                                    تحديد الكل
                                </button>
                                <button type="button" @click="deselectAllDrivers()"
                                    class="btn btn-sm btn-outline-secondary">
                                    إلغاء الكل
                                </button>
                            </div>

                            <template x-for="(shift, shiftIndex) in selectedCarDrivers" :key="shiftIndex">
                                <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="font-bold text-primary mb-2 flex items-center gap-2">
                                        <span>🕐</span>
                                        <span x-text="shift.shift_name"></span>
                                    </div>

                                    <!-- السائق الرئيسي -->
                                    <div x-show="shift.primary"
                                        class="flex items-center gap-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded mb-2">
                                        <input type="checkbox" :name="'end_drivers[' + shift.shift_id + '][primary]'"
                                            :id="'primary_' + shift.shift_id" value="1" x-model="shift.end_primary"
                                            class="form-checkbox text-primary">
                                        <label :for="'primary_' + shift.shift_id" class="flex-1 cursor-pointer">
                                            <span class="text-sm font-medium">🚗 رئيسي:</span>
                                            <span x-text="shift.primary" class="text-blue-600 dark:text-blue-400"></span>
                                        </label>
                                    </div>

                                    <!-- السائق الاحتياطي -->
                                    <div x-show="shift.backup"
                                        class="flex items-center gap-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                        <input type="checkbox" :name="'end_drivers[' + shift.shift_id + '][backup]'"
                                            :id="'backup_' + shift.shift_id" value="1" x-model="shift.end_backup"
                                            class="form-checkbox text-warning">
                                        <label :for="'backup_' + shift.shift_id" class="flex-1 cursor-pointer">
                                            <span class="text-sm font-medium">🔄 احتياط:</span>
                                            <span x-text="shift.backup" class="text-yellow-600 dark:text-yellow-400"></span>
                                        </label>
                                    </div>

                                    <div x-show="!shift.primary && !shift.backup" class="text-gray-400 text-sm">
                                        لا يوجد سائقين في هذا الشفت
                                    </div>
                                </div>
                            </template>

                            <div x-show="selectedCarDrivers.length === 0"
                                class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-center text-gray-500">
                                لا يوجد سائقين معينين لهذه السيارة
                            </div>
                        </div>

                        <!-- سبب الإنهاء -->
                        <div class="space-y-2">
                            <label for="end_reason" class="block font-medium text-gray-700 dark:text-gray-300">
                                سبب الإنهاء (اختياري)
                            </label>
                            <textarea name="end_reason" id="end_reason" rows="2" class="form-input w-full"
                                placeholder="أدخل سبب إنهاء التكليف..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEndModal = false" class="btn btn-outline-secondary">
                            إلغاء
                        </button>
                        <button type="submit" class="btn btn-danger" :disabled="!hasSelectedDrivers()">
                            تأكيد الإنهاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CSS لتوسيط النصوص داخل الجدول -->
    <style>
        #myTable2 td,
        #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }

        /* عمود السائقين يكون محاذاة لليمين */
        #myTable2 td:nth-child(5) {
            text-align: right !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            // جدول البيانات
            Alpine.data('multipleTable', () => ({
                datatable2: null,
                showEndModal: false,
                selectedCarId: null,
                selectedCarNumber: '',
                selectedCarDrivers: [], // بيانات السائقين حسب الشفتات

                openEndModal(carId, carNumber, carDrivers) {
                    this.selectedCarId = carId;
                    this.selectedCarNumber = carNumber;
                    // إضافة حقول end_primary و end_backup لكل شفت
                    this.selectedCarDrivers = carDrivers.map(d => ({
                        ...d,
                        end_primary: false,
                        end_backup: false
                    }));
                    this.showEndModal = true;
                },

                selectAllDrivers() {
                    this.selectedCarDrivers.forEach(shift => {
                        if (shift.primary) shift.end_primary = true;
                        if (shift.backup) shift.end_backup = true;
                    });
                },

                deselectAllDrivers() {
                    this.selectedCarDrivers.forEach(shift => {
                        shift.end_primary = false;
                        shift.end_backup = false;
                    });
                },

                hasSelectedDrivers() {
                    return this.selectedCarDrivers.some(shift => shift.end_primary || shift.end_backup);
                },

                init() {
                    const self = this;
                    // بيانات الشفتات
                    const shiftsData = {!! json_encode($shifts->pluck('name', 'id')) !!};

                    // تمرير بيانات السيارات من السيرفر إلى JavaScript
                    const tableData = {!! json_encode(
                        $listCars->map(function ($car) {
                            // جمع السائقين من جدول car_drivers الجديد
                            $carDriversData = [];
                            if ($car->activeCarDrivers && $car->activeCarDrivers->count() > 0) {
                                foreach ($car->activeCarDrivers as $cd) {
                                    $shiftId = $cd->shift_id;
                                    if (!isset($carDriversData[$shiftId])) {
                                        $carDriversData[$shiftId] = [
                                            'shift_id' => $shiftId,
                                            'shift_name' => $cd->shift ? $cd->shift->name : 'غير محدد',
                                            'primary' => null,
                                            'backup' => null,
                                        ];
                                    }
                                    if ($cd->driver_type === 'primary') {
                                        $carDriversData[$shiftId]['primary'] = $cd->driver ? $cd->driver->fullname : '';
                                    } else {
                                        $carDriversData[$shiftId]['backup'] = $cd->driver ? $cd->driver->fullname : '';
                                    }
                                }
                            }
                    
                            return [
                                'id' => $car->id,
                                'car_type' => $car->carType->name ?? 'غير محدد',
                                'car_number' => $car->car_number ?? 'غير متوفر',
                                'car_model' => $car->car_model ?? 'غير متوفر',
                                'is_active' => $car->is_active ? 'فعالة' : 'غير فعالة',
                                'car_drivers' => array_values($carDriversData),
                                'has_any_driver' => count($carDriversData) > 0,
                                'add_date' => $car->add_date ?? 'غير محدد',
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف الجدول - عرض السائقين من الجدول الجديد
                    const rows = tableData.map(c => {
                        // بناء محتوى عمود السائقين من car_drivers
                        let driversHtml = '';

                        if (c.car_drivers && c.car_drivers.length > 0) {
                            driversHtml = '<div class="flex flex-col gap-2">';
                            c.car_drivers.forEach(shiftData => {
                                driversHtml +=
                                    '<div class="p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs border-r-4 border-primary">';
                                driversHtml +=
                                    '<div class="font-bold text-primary mb-1">🕐 ' +
                                    shiftData.shift_name + '</div>';
                                if (shiftData.primary) {
                                    driversHtml +=
                                        '<div class="text-blue-600 dark:text-blue-400">🚗 رئيسي: ' +
                                        shiftData.primary + '</div>';
                                }
                                if (shiftData.backup) {
                                    driversHtml +=
                                        '<div class="text-yellow-600 dark:text-yellow-400">🔄 احتياط: ' +
                                        shiftData.backup + '</div>';
                                }
                                driversHtml += '</div>';
                            });
                            driversHtml += '</div>';
                        } else {
                            driversHtml = '<span class="text-gray-400">لا يوجد سائقين</span>';
                        }

                        return [
                            c.car_type,
                            c.car_number,
                            c.car_model,
                            c.is_active,
                            driversHtml,
                            c.add_date,
                            c.id, // عمود عرض (التعديل)
                            JSON.stringify({
                                id: c.id,
                                car_number: c.car_number,
                                car_drivers: c.car_drivers,
                                has_any_driver: c.has_any_driver
                            }) // عمود إنهاء التكليف
                        ];
                    });

                    // إنشاء الجدول
                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'نوع السيارة',
                                'رقم السيارة',
                                'الموديل',
                                'الحالة',
                                'السائقين',
                                'تاريخ الإضافة',
                                'عرض',
                                'إنهاء التكليف'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 0,
                                className: 'text-center'
                            },
                            {
                                select: 1,
                                className: 'text-center'
                            },
                            {
                                select: 2,
                                className: 'text-center'
                            },
                            {
                                select: 3,
                                className: 'text-center',
                                render: (data) => {
                                    if (data === 'فعالة') {
                                        return '<span class="badge bg-success/20 text-success rounded-full px-3 py-1">فعالة</span>';
                                    } else {
                                        return '<span class="badge bg-danger/20 text-danger rounded-full px-3 py-1">غير فعالة</span>';
                                    }
                                }
                            },
                            {
                                select: 4,
                                className: 'text-right',
                                render: (data) => {
                                    return data; // HTML جاهز
                                }
                            },
                            {
                                select: 5,
                                className: 'text-center'
                            },
                            {
                                select: 6,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url = '/ConcreteERP/cars/' + id +
                                        '&EditCarInformation/edit';

                                    return '<a href="' + url +
                                        '" class="btn btn-sm btn-outline-primary" title="تعديل بيانات السيارة"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> تعديل</a>';
                                },
                            },
                            {
                                select: 7,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const carData = JSON.parse(data);
                                    const id = carData.id;
                                    const hasAnyDriver = carData.has_any_driver;
                                    const carDriversJson = encodeURIComponent(JSON
                                        .stringify(carData.car_drivers));

                                    if (hasAnyDriver) {
                                        return `<button type="button" class="btn btn-sm btn-outline-danger end-driver-btn" data-car-id="${id}" data-car-number="${carData.car_number}" data-car-drivers="${carDriversJson}" title="إنهاء تكليف السائقين"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg> إنهاء</button>`;
                                    } else {
                                        return '<span class="text-gray-400 text-sm">-</span>';
                                    }
                                },
                            },
                        ],
                        firstLast: true,
                        labels: {
                            perPage: '{select}',
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}',
                        },
                    });

                    // Event delegation لأزرار إنهاء التكليف
                    document.querySelector('#myTable2').addEventListener('click', (e) => {
                        const btn = e.target.closest('.end-driver-btn');
                        if (btn) {
                            const carId = btn.dataset.carId;
                            const carNumber = btn.dataset.carNumber;
                            const carDrivers = JSON.parse(decodeURIComponent(btn.dataset
                                .carDrivers));
                            self.openEndModal(carId, carNumber, carDrivers);
                        }
                    });
                },
            }));
        });
    </script>
@endsection
