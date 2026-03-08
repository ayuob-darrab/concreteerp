@extends('layouts.app')

@section('page-title', 'عرض او اضافة مقاول جديد 👷')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="contractorModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <i class="fas fa-user-tie"></i>
                        <span>إضافة مقاول جديد 👷</span>
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
                                    إضافة مقاول جديد 👷</h5>
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'contractors.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <!-- الفرع -->
                                    <div class="space-y-3">
                                        <label for="branch_id" class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                                        </label>
                                        <select name="branch_id" id="branch_id" class="form-select" required>
                                            <option value="" disabled selected>اختر الفرع</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- اسم الشركة / المقاول -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشركة / المقاول <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="contract_name" placeholder="أدخل اسم الشركة أو المقاول"
                                            value="{{ old('contract_name') }}" class="form-input" required>
                                        @error('contract_name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- اسم المدير المسؤول -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم المدير المسؤول</span>
                                        </label>
                                        <input type="text" name="contract_adminstarter"
                                            placeholder="أدخل اسم المدير المسؤول" value="{{ old('contract_adminstarter') }}"
                                            class="form-input">
                                        @error('contract_adminstarter')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الهاتف 1 -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الهاتف 1</span>
                                        </label>
                                        <input type="text" name="phone1" placeholder="أدخل الهاتف الأول"
                                            value="{{ old('phone1') }}" class="form-input" pattern="\d{1,11}" minlength="1"
                                            maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                                        @error('phone1')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الهاتف 2 -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الهاتف 2</span>
                                        </label>
                                        <input type="text" name="phone2" placeholder="أدخل الهاتف الثاني"
                                            value="{{ old('phone2') }}" class="form-input" pattern="\d{1,11}" minlength="1"
                                            maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                                        @error('phone2')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الرصيد الافتتاحي -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الرصيد الافتتاحي</span>
                                        </label>
                                        <input type="text" name="opening_balance" placeholder="أدخل الرصيد الافتتاحي"
                                            oninput="formatPrice(this)" class="form-input" step="0.01">
                                        @error('opening_balance')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- العنوان -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">العنوان</span>
                                        </label>
                                        <input type="text" name="address" placeholder="أدخل العنوان"
                                            value="{{ old('address') }}" class="form-input">
                                        @error('address')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <!-- رفع شعار الشركة -->
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

                                    <div class="flex items-center gap-3">
                                        <button type="submit" name="active" value="AddNewContractor"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>حفظ المقاول</span>
                                        </button>

                                        <button type="reset" @click="openModal = false"
                                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i>
                                            <span>إلغاء</span>
                                        </button>
                                    </div>


                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </h3>

            <!-- جدول المقاولين -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200"></table>
        </div>
    </div>

    <!-- CSS لتوسيط النصوص داخل الجدول -->
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
            Alpine.data('contractorModal', () => ({
                openModal: false
            }));

            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const tableData = {
                        !!json_encode(
                            $Contractor - > map(function($c) {
                                return [
                                    'id' => $c - > id,
                                    'branch' => $c - > branch - > branch_name ?? 'غير محدد',
                                    'contract_name' => $c - > contract_name ?? 'غير محدد',
                                    'contract_adminstarter' => $c - >
                                    contract_adminstarter ?? 'غير محدد',
                                    'phone1' => $c - > phone1 ?? 'غير متوفر',
                                    'phone2' => $c - > phone2 ?? 'غير متوفر',
                                    'opening_balance' => $c - > opening_balance ?? 0,
                                    'isactive' => $c - > isactive ? 'فعال' : 'غير فعال',
                                    'address' => $c - > address ?? 'غير محدد',
                                    'createdate' => $c - > createdate ?? 'غير محدد',
                                    'note' => $c - > note ?? 'لا يوجد',
                                ];
                            }),
                        ) !!
                    };

                    const rows = tableData.map(c => [
                        c.branch,
                        c.contract_name,
                        c.contract_adminstarter,
                        c.phone1,
                        c.phone2,
                        c.opening_balance,
                        c.isactive,
                        c.address,
                        c.createdate,
                        c.note,
                        c.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الفرع',
                                'اسم الشركة / المقاول',
                                'مدير المقاول',
                                'الهاتف 1',
                                'الهاتف 2',
                                'الرصيد الافتتاحي',
                                'الحالة',
                                'العنوان',
                                'تاريخ الإنشاء',
                                'ملاحظات',
                                'تعديل'
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
                                className: 'text-center'
                            },
                            {
                                select: 4,
                                className: 'text-center'
                            },
                            {
                                select: 5,
                                className: 'text-center'
                            },
                            {
                                select: 6,
                                className: 'text-center'
                            },
                            {
                                select: 7,
                                className: 'text-center'
                            },
                            {
                                select: 8,
                                className: 'text-center'
                            },
                            {
                                select: 9,
                                className: 'text-center'
                            },
                            {
                                select: 10,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/contractors/${id}&EditContractors/edit`;
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
                            },
                        ],
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





'status' => $order->status->name_code ?? 'جديد', 'request_date' => $order->request_date, 'review_user' =>
$order->reviewer->fullname ?? '-', 'review_date' => $order->review_date ?? '-', 'review_note' => $order->review_note ??
'-', 'accept_user' => $order->accepter->fullname ?? '-', 'accept_date' => $order->accept_date ?? '-', 'accept_note' =>
$order->accept_note ?? '-', 'rejected_user' => $order->rejecter->fullname ?? '-', 'rejected_date' =>
$order->rejected_date ?? '-', 'rejected_note' => $order->rejected_note ?? '-', 'note' => $order->note ?? '-', 'price' =>
$order->price ?? '-',
