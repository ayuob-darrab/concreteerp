@extends('layouts.app')

@section('page-title', 'عرض و اضافة فرع')

@section('content')

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <div x-data="multipleTable">
        <div class="panel mt-6">
            <div class="flex items-center justify-between mb-5 md:absolute md:top-[25px] md:w-full md:pr-4">
                <!-- زر فتح المودال -->

                <div x-data="branchModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>إضافة فرع الى : {{ Auth::user()->CompanyName->name }}</span>
                    </button>

                    <!-- المودال -->
                    <div x-show="openModal" x-cloak
                        class="fixed inset-0 z-50 flex items-start justify-center pt-10 bg-black/50 overflow-y-auto">
                        <div x-show="openModal" x-transition
                            class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-5xl shadow-2xl border border-gray-200 dark:border-gray-700 m-4">

                            <!-- رأس المودال -->
                            <div class="flex justify-between items-center p-4 border-b bg-indigo-100 dark:bg-indigo-900">
                                <h5
                                    class="font-bold text-lg text-center w-full text-gray-50 dark:text-white bg-gray-700 dark:bg-gray-900 py-3 rounded-lg shadow-md">
                                    إضافة فرع جديد: {{ Auth::user()->CompanyName->name }}</h5>
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'companyBranch.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- العمود الأول -->
                                    <div class="space-y-4">
                                        <!-- اسم الفرع -->
                                        <div>
                                            <label class="block font-medium text-gray-700 dark:text-gray-200">اسم الفرع
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="branch_name" id="branch_name"
                                                placeholder="أدخل اسم الفرع" value="{{ old('breanch_name') }}"
                                                class="form-input w-full" required>
                                            @error('breanch_name')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- مدير الفرع -->
                                        <div>
                                            <label class="block font-medium text-gray-700 dark:text-gray-200">مدير
                                                الفرع</label>
                                            <input type="text" name="branch_admin" id="breanch_admin"
                                                value="{{ old('breanch_admin') }}" placeholder="أدخل اسم مدير الفرع"
                                                class="form-input w-full" required>
                                            @error('breanch_admin')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <br>
                                        <!-- البريد الإلكتروني -->
                                        <div>
                                            <label class="block font-medium text-gray-700 dark:text-gray-200">البريد
                                                الإلكتروني</label>
                                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                                placeholder="example@email.com" class="form-input w-full" required>
                                            @error('email')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    <!-- العمود الثاني -->
                                    <div class="space-y-4">
                                        <!-- المحافظة -->
                                        <div>
                                            <label class="block font-medium text-gray-700 dark:text-gray-200">المحافظة <span
                                                    class="text-danger">*</span></label>
                                            <select name="city_id" id="city_id" class="form-input w-full" required>
                                                <option value="">اختر المحافظة</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name_ar }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('city_id')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- الهاتف -->
                                        <div>
                                            <label class="block font-medium text-gray-700 dark:text-gray-200">رقم
                                                الهاتف</label>
                                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                                placeholder="أدخل رقم الهاتف" class="form-input w-full" required
                                                pattern="\d{11}" maxlength="11" minlength="11"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                            @error('phone')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                            <small class="text-gray-500">يجب أن يكون الرقم مكوّنًا من 11 رقمًا فقط</small>
                                        </div>



                                        <!-- العنوان -->
                                        <div>
                                            <label
                                                class="block font-medium text-gray-700 dark:text-gray-200">العنوان</label>
                                            <input type="text" name="address" id="address"
                                                value="{{ old('address') }}" placeholder="أدخل عنوان الفرع"
                                                class="form-input w-full" required>
                                            @error('address')
                                                <div class="text-danger text-sm">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4">
                                    <button type="reset" @click="openModal = false"
                                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                        <i class="fas fa-times-circle"></i>
                                        <span>إلغاء</span>
                                    </button>

                                    <button type="submit" name="active" value="Newbranch"
                                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                        <i class="fas fa-check-circle"></i>
                                        <span>حفظ الفرع</span>
                                    </button>
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('branchModal', () => ({
                            openModal: false
                        }));
                    });
                </script>

            </div>

            <table id="myTable2" class="whitespace-nowrap">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قا ئمة فروع شركة : {{ Auth::user()->CompanyName->name }}
                </caption>
            </table>
        </div>

    </div>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('companyModal', () => ({
                openModal: false
            }));

            Alpine.data('multipleTable', () => ({

                init() {
                    const tableData = {!! json_encode(
                        $allbranchs->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'breanch_name' => $b->branch_name,
                                'city_name' => $b->cityName->name_ar,
                                'breanch_admin' => $b->branch_admin,
                                'phone' => $b->phone,
                                'email' => $b->email,
                                'address' => $b->address,
                                'is_active' => $b->is_active ? 'مفعل' : 'معطل',
                                'created_date' => \Carbon\Carbon::parse($b->created_date)->format('d-m-Y'),
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(b => [
                        b.breanch_name,
                        b.city_name,
                        b.breanch_admin,
                        b.phone,
                        b.email,
                        b.address,
                        b.is_active,
                        b.created_date,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الفرع',
                                'المدينة',
                                'الإدارة',
                                'الهاتف',
                                'البريد الإلكتروني',
                                'العنوان',
                                'حالة الفرع',
                                'تاريخ الإنشاء',
                                'الإجراء'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 50, 100],
                        columns: [{
                            select: 8,
                            sortable: false,
                            className: 'text-center',

                            render: (data) => {
                                const id = data;
                                const editUrl =
                                    `${baseUrl}/companyBranch/${id}&edit_branch/edit`;
                                const mapUrl =
                                    `${baseUrl}/companyBranch/${id}&Location/edit`;

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
