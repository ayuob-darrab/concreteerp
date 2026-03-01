@extends('layouts.app')

@section('page-title', 'عرض الشركات 🏢')

@section('content')
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    {{-- رسائل النجاح والخطأ --}}
    @if (session('success'))
        <div class="mb-5 flex items-center p-3.5 rounded text-white bg-success">
            <span class="ltr:pr-2 rtl:pl-2">
                <strong class="ltr:mr-1 rtl:ml-1">✓</strong>{{ session('success') }}
            </span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center p-3.5 rounded text-white bg-danger">
            <span class="ltr:pr-2 rtl:pl-2">
                <strong class="ltr:mr-1 rtl:ml-1">✗</strong>{{ session('error') }}
            </span>
        </div>
    @endif

    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <!-- زر للانتقال لصفحة إضافة شركة -->
                <a href="{{ route('companies.create') }}" class="btn btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة شركة جديدة 🏢</span>
                </a>
            </h3>

            <!-- جدول الشركات -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200"></table>
        </div>

        <!-- مودال خرائط Leaflet -->
        {{-- <div x-show="mapModalOpen" x-cloak @click.self="closeMapModal()"
            class="fixed inset-0 z-[99999] bg-black/70 flex flex-col" style="backdrop-filter: blur(4px);">

            <div class="relative flex-1">
                <!-- الخريطة -->
                <div id="mapContainer" class="w-full h-full"></div>

                <!-- عنوان المودال -->
                <div
                    class="absolute top-6 left-1/2 transform -translate-x-1/2 bg-white dark:bg-gray-800 px-6 py-3 rounded-lg shadow-2xl z-[100000] border-2 border-gray-200 dark:border-gray-700">
                    <h5 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        موقع الشركة
                    </h5>
                </div>

                {!! Form::open([
                    'route' => 'companies.store',
                    'method' => 'POST',
                    'autocomplete' => 'off',
                    'files' => true,
                ]) !!}
                <!-- معلومات الشركة والإحداثيات -->
                <div
                    class="absolute bottom-24 left-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-2xl z-[100000] border-2 border-gray-200 dark:border-gray-700 flex flex-row gap-6 items-start">

                    <!-- اسم الشركة وكودها -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <label class="font-bold" for="companyName">اسم الشركة:</label>
                            <input id="companyName" type="text" x-model="selectedCompanyName"
                                class="border border-gray-300 rounded px-2 py-1 w-48">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="font-bold" for="companyCode">كود الشركة:</label>
                            <input id="companyCode" name="companyCode" type="text" x-model="selectedCompanyCode"
                                class="border border-gray-300 rounded px-2 py-1 w-32">
                        </div>
                    </div>


                    <!-- الإحداثيات -->
                    <div class="flex flex-col gap-2 text-base text-black">
                        <div class="font-extrabold">الإحداثيات:</div>
                        <div class="flex items-center gap-2">
                            <label for="latitude" class="font-bold">خط العرض:</label>
                            <input id="latitude" name="latitude" type="text" x-model="currentLatitude"
                                class="border border-gray-300 rounded px-2 py-1 text-black w-32">
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="longitude" class="font-bold">خط الطول:</label>
                            <input id="longitude" name="longitude" type="text" x-model="currentLongitude"
                                class="border border-gray-300 rounded px-2 py-1 text-black w-32">
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="flex flex-col gap-2">
                        <button @click="saveCompanyLocation()" type="submit" name="active" value="AddaddressGoogle"
                            class="bg-green-600 hover:bg-green-700 text-black font-extrabold px-4 py-2 rounded-lg shadow-md text-lg transition-all duration-200 transform hover:scale-105 border-2 border-green-400">
                            حفظ الموقع
                        </button>

                        <button @click="closeMapModal()" type="button"
                            class="bg-red-600 hover:bg-red-700 text-black font-extrabold px-4 py-2 rounded-lg shadow-md text-lg transition-all duration-200 transform hover:scale-105 border-2 border-red-400">
                            إغلاق
                        </button>
                    </div>

                </div>

                {!! Form::close() !!}


            </div>
        </div> --}}


    </div>

    <style>
        #myTable2 td,
        #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    {{-- <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('companyModal', () => ({
                openModal: false
            }));

            Alpine.data('multipleTable', () => ({
                datatable2: null,
                mapModalOpen: false,
                selectedCompanyId: null,
                selectedCompanyName: '',
                selectedCompanyCode: '',
                map: null,
                marker: null,
                mapInitialized: false,
                currentLatitude: 33.3152,
                currentLongitude: 44.3661,

                openMapModal(id) {
                    this.selectedCompanyId = id;
                    const company = {!! json_encode(
                        $companies->map(function ($c) {
                            return [
                                'id' => $c->id,
                                'code' => $c->code ?? 'غير محدد',
                                'name' => $c->name ?? 'غير محدد',
                            ];
                        }),
                    ) !!}.find(c => c.id === id);

                    if (company) {
                        this.selectedCompanyName = company.name;
                        this.selectedCompanyCode = company.code;
                    }

                    this.mapModalOpen = true;
                    this.$nextTick(() => {
                        if (!this.mapInitialized) {
                            this.initMap();
                        } else {
                            this.map.invalidateSize();
                        }
                    });
                },

                closeMapModal() {
                    this.mapModalOpen = false;
                },

                initMap() {
                    const defaultLatLng = [this.currentLatitude, this.currentLongitude];
                    this.map = L.map('mapContainer').setView(defaultLatLng, 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);

                    this.marker = L.marker(defaultLatLng, {
                        draggable: true
                    }).addTo(this.map);

                    this.marker.on('dragend', () => {
                        const pos = this.marker.getLatLng();
                        this.currentLatitude = pos.lat.toFixed(6);
                        this.currentLongitude = pos.lng.toFixed(6);
                    });

                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.currentLatitude = e.latlng.lat.toFixed(6);
                        this.currentLongitude = e.latlng.lng.toFixed(6);
                    });

                    this.mapInitialized = true;
                },

                saveCompanyLocation() {
                    fetch(`/ConcreteERP/companies/${this.selectedCompanyId}/update-location`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                latitude: this.currentLatitude,
                                longitude: this.currentLongitude
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.Swal && Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحفظ!',
                                    text: `تم حفظ موقع الشركة بنجاح\nخط العرض: ${this.currentLatitude}\nخط الطول: ${this.currentLongitude}`,
                                    confirmButtonText: 'حسناً'
                                });
                                this.closeMapModal();
                            } else {
                                throw new Error(data.message || 'حدث خطأ');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.Swal && Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء حفظ الموقع',
                                confirmButtonText: 'حسناً'
                            });
                        });
                },

                init() {
                    const tableData = {!! json_encode(
                        $companies->map(function ($c) {
                            return [
                                'id' => $c->id,
                                'code' => $c->code ?? 'غير محدد',
                                'name' => $c->name ?? 'غير محدد',
                                'managername' => $c->managername ?? 'غير محدد',
                                'city' => $c->city->name_ar ?? 'غير محددة',
                                'phone' => $c->phone ?? 'غير متوفر',
                                'email' => $c->email ?? 'غير متوفر',
                                'address' => $c->address ?? 'غير محدد',
                                'is_active' => $c->is_active ? 'فعالة' : 'غير فعالة',
                              
                                'note' => $c->note ?? 'لا يوجد',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(c => [
                        c.code, c.name, c.managername , c.city, c.phone, c.email, c.address,
                        c.is_active, c.note, c.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: ['كود الشركة', 'اسم الشركة', 'مدير الشركة','المدينة', 'الهاتف',
                                'البريد الإلكتروني', 'العنوان', 
                                'الحالة', 'ملاحظات', 'تعديل'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 50, 100],
                        columns: [{
                            select: 9,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const editUrl =
                                    `/ConcreteERP/companies/${id}&edit_company/edit`;
                                return `
                                <div class="flex items-center justify-center gap-2">
                                    <a href="${editUrl}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
                                        </svg>
                                    </a>

                                    <button type="button" class="text-blue-600 hover:text-blue-800" @click="openMapModal(${id})" x-tooltip="خرائط">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                `;
                            },
                        }],
                        firstLast: true,
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });
                },
            }));
        });
    </script> --}}

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('companyModal', () => ({
                openModal: false
            }));

            Alpine.data('multipleTable', () => ({
                datatable2: null,
                mapModalOpen: false,
                selectedCompanyId: null,
                selectedCompanyName: '',
                selectedCompanyCode: '',
                map: null,
                marker: null,
                mapInitialized: false,
                currentLatitude: 33.3152,
                currentLongitude: 44.3661,

                openMapModal(id) {}, // لم تعد مستخدمة

                closeMapModal() {
                    this.mapModalOpen = false;
                },

                initMap() {}, // لن يتم استدعاؤه لأننا ألغينا المودل

                saveCompanyLocation() {}, // لم يعد مستخدم

                init() {
                    const tableData = {!! json_encode(
                        $companies->map(function ($c) {
                            return [
                                'id' => $c->id,
                                'code' => $c->code ?? 'غير محدد',
                                'name' => $c->name ?? 'غير محدد',
                                'managername' => $c->managername ?? 'غير محدد',
                                'city' => $c->city->name_ar ?? 'غير محددة',
                                'phone' => $c->phone ?? 'غير متوفر',
                                'email' => $c->email ?? 'غير متوفر',
                                'address' => $c->address ?? 'غير محدد',
                                'is_active' => $c->is_active ? 'فعالة' : 'غير فعالة',
                                'note' => $c->note ?? 'لا يوجد',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(c => [
                        c.code, c.name, c.managername, c.city, c.phone, c.email, c.address,
                        c.is_active, c.note, c.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: ['كود الشركة', 'اسم الشركة', 'مدير الشركة', 'المدينة',
                                'الهاتف',
                                'البريد الإلكتروني', 'العنوان',
                                'الحالة', 'ملاحظات', 'تعديل'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 50, 100],
                        columns: [{
                            select: 9,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const editUrl =
                                    `/ConcreteERP/companies/${id}&edit_company/edit`;
                                const mapUrl =
                                    `/ConcreteERP/companies/${id}&Location/edit`;

                                return `
                                <div style="display:flex; align-items:center; justify-content:center; gap:12px; width:100%;">
                                    
                                    <a href="${editUrl}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 transition-transform duration-200 hover:scale-110"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
                                        </svg>
                                    </a>

                                    <a href="${mapUrl}" class="text-blue-600 hover:text-blue-800" x-tooltip="اضافة الموقع على الخرائط">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 transition-transform duration-200 hover:scale-110"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        </svg>
                                    </a>

                                </div>  `;
                            },

                        }],
                        firstLast: true,
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });
                },
            }));
        });
    </script>








@endsection
