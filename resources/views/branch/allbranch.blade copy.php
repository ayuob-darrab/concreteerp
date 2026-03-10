@extends('layouts.app')

@section('page-title', 'لوحة التحكم الرئيسية')

@section('content')
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
                                {{-- <button @click="openModal = false"
                                    class="absolute right-4 text-gray-500 hover:text-gray-700 text-2xl">اغلاق</button> --}}
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'companyBranch.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                {!! Form::hidden('company_code', Auth::user()->CompanyName->code) !!}
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

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
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                            select: 8,
                            sortable: true,
                            render: (data) => {
                                const id = data;
                                const url =
                                    `{{ url('') }}/companyBranch/${id}&edit_branch/edit`;
                                return `
                                        <div class="flex items-center justify-center">
                                            <a href="${url}" class="text-blue-600 hover:text-blue-800" x-tooltip="تعديل">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                                    <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5" />
                                                    <path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5" />
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
                        }, ],
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}',
                        },
                    });
                },
            }));
        });
    </script>
@endsection
