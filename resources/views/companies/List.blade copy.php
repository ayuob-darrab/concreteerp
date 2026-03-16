@extends('layouts.app')

@section('page-title', 'عرض أو إضافة شركة جديدة 🏢')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="companyModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <i class="fas fa-building"></i>
                        <span>إضافة شركة جديدة 🏢</span>
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
                                    إضافة شركة جديدة 🏢
                                </h5>
                                {{-- <button @click="openModal = false"
                                    class="absolute right-4 text-gray-500 hover:text-gray-700 text-2xl">اغلاق</button> --}}
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'companies.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <!-- اسم الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشركة <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="name" placeholder="أدخل اسم الشركة"
                                            value="{{ old('name') }}" class="form-input" required>
                                        @error('name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- المحافظة -->
                                    <div class="space-y-3">
                                        <label for="city_id" class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">المحافظة <span class="text-danger">*</span></span>
                                        </label>
                                        <select name="city_id" id="city_id" class="form-select" required>
                                            <option value="" disabled selected>اختر المحافظة</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name_ar }}</option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الهاتف -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الهاتف</span>
                                        </label>
                                        <input type="text" name="phone" placeholder="أدخل رقم الهاتف"
                                            value="{{ old('phone') }}" class="form-input" maxlength="11" minlength="8">
                                        @error('phone')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- البريد الإلكتروني -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">البريد الإلكتروني</span>
                                        </label>
                                        <input type="email" name="email" placeholder="example@domain.com"
                                            value="{{ old('email') }}" class="form-input">
                                        @error('email')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- عنوان الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">العنوان</span>
                                        </label>
                                        <input type="text" name="address" placeholder="أدخل عنوان الشركة"
                                            value="{{ old('address') }}" class="form-input">
                                        @error('address')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- شعار الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">شعار الشركة</span>
                                        </label>
                                        <input type="file" name="logo" class="form-input" accept="image/*">
                                        @error('logo')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    
                                    <!-- عنوان الشركة -->
                                <!-- الزر داخل الفورم -->
                                




                                    <!-- الأزرار -->
                                    <div class="flex gap-4 lg:col-span-2 justify-center mt-4">
                                        <button type="submit" name="active" value="AddNewCompany"
                                            class="btn btn-primary flex items-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i> حفظ الشركة
                                        </button>
                                        <button type="reset" @click="openModal = false"
                                            class="btn btn-outline-secondary flex items-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i> إلغاء
                                        </button>
                                    </div>

                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('companyModal', () => ({
                openModal: false
            }));

            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const tableData = {!! json_encode(
                        $companies->map(function ($c) {
                            return [
                                'id' => $c->id,
                                'code' => $c->code ?? 'غير محدد',
                                'name' => $c->name ?? 'غير محدد',
                                'city' => $c->city->name_ar ?? 'غير محددة',
                                'phone' => $c->phone ?? 'غير متوفر',
                                'email' => $c->email ?? 'غير متوفر',
                                'address' => $c->address ?? 'غير محدد',
                                'is_active' => $c->is_active ? 'فعالة' : 'غير فعالة',
                                'userAdmin' => $c->userAdmin ?? 'غير محدد',
                                'note' => $c->note ?? 'لا يوجد',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(c => [
                        c.code,
                        c.name,
                        c.city,
                        c.phone,
                        c.email,
                        c.address,
                        c.userAdmin,
                        c.is_active,
                        c.note,
                        c.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'كود الشركة',
                                'اسم الشركة',
                                'المحافظة',
                                'الهاتف',
                                'البريد الإلكتروني',
                                'العنوان',
                                'المستخدم المسؤول',
                                'الحالة',
                                'ملاحظات',
                                'تعديل'
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
                                const url =
                                    `{{ url('') }}/companies/${id}&edit_company/edit`;
                                return `
                                        <div class="flex items-center justify-center">
                                            <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    `;
                            },
                        }, ],
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