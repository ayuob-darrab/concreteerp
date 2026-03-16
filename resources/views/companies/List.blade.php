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

    


    </div>

    <style>
        #myTable2 td,
        #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

  

    <script>
        const baseUrl = '{{ url('/') }}';
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
                            headings: ['كود الشركة', 'اسم الشركة', 'مدير الشركة', 'المحافظة',
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
                                    `${baseUrl}/companies/${id}&edit_company/edit`;
                                const mapUrl =
                                    `${baseUrl}/companies/${id}&Location/edit`;

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
