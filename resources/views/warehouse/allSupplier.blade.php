@extends('layouts.app')

@section('page-title', 'عرض و اضافة المواد الرئيسية في المستودع')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="carTypeModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <i class="fas fa-car"></i>
                        <span>إضافة مورد جديدة</span>
                    </button>

                    <!-- المودال -->
                    <div x-show="openModal" x-cloak
                        class="fixed inset-0 z-50 flex items-start justify-center pt-10 bg-black/50 overflow-y-auto">
                        <div x-show="openModal" x-transition
                            class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-3xl shadow-2xl border border-gray-200 dark:border-gray-700 m-4">

                            <!-- رأس المودال -->
                            <div class="flex justify-between items-center p-4 border-b bg-indigo-100 dark:bg-indigo-900">
                                <h5
                                    class="font-bold text-lg text-center w-full text-gray-50 dark:text-white bg-gray-700 dark:bg-gray-900 py-3 rounded-lg shadow-md">
                                    إضافة مورد جديد</h5>
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'warehouse.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                    <!-- اسم المورد -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم المورد <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="supplier_name" id="supplier_name"
                                            placeholder="أدخل اسم المورد" value="{{ old('supplier_name') }}"
                                            class="form-input" required>
                                        @error('supplier_name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- اسم الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشركة</span>
                                        </label>
                                        <input type="text" name="company_name" id="company_name" required
                                            placeholder="أدخل اسم الشركة" value="" class="form-input">
                                        @error('company_name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الفرع -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                                        </label>
                                        <select name="branch_id" id="branch_id" class="form-select" required>
                                            <option value="">اختر الفرع</option>
                                            @foreach ($Branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الرصيد الافتتاحي -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الرصيد الافتتاحي</span>
                                        </label>
                                        <input type="text" name="opening_balance" id="opening_balance" required
                                            placeholder="أدخل الرصيد الافتتاحي" step="0.01" min="0" value=""
                                            oninput="formatPrice(this)" class="form-input">
                                        @error('opening_balance')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- رقم الهاتف -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">رقم الهاتف</span>
                                        </label>
                                        <input type="text" name="phone" id="phone"
                                            placeholder="أدخل رقم الهاتف (11 رقمًا)" value="{{ old('phone') }}"
                                            class="form-input" maxlength="11" pattern="\d{11}"
                                            title="الرجاء إدخال رقم هاتف يتكون من 11 رقمًا فقط"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)" required>

                                        @error('phone')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- العنوان -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">العنوان</span>
                                        </label>
                                        <input type="text" name="address" id="address" required
                                            placeholder="أدخل عنوان المورد" value="{{ old('address') }}" class="form-input">
                                        @error('address')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الملاحظات -->
                                    {{-- <div class="space-y-3 lg:col-span-2"> --}}
                                    <div class="space-y-3 ">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="space-y-3 ">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark"></span>
                                        </label>

                                        <div
                                            class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                                            <button type="submit" name="active" value="AddNewSupplier"
                                                class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-check-circle"></i>
                                                <span>حفظ المورد</span>
                                            </button>

                                            <button type="reset" @click="openModal = false"
                                                class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-times-circle"></i>
                                                <span>إلغاء</span>
                                            </button>
                                        </div>
                                    </div>


                                </div>


                                {!! Form::close() !!}
                            </div>

                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('carTypeModal', () => ({
                            openModal: false
                        }));
                    });
                </script>

            </h3>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap  w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة بجميع الموردين في الشركة
                </caption>
            </table>
        </div>
    </div>


    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const tableData = {!! json_encode(
                        $allSuppliers->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'supplier_name' => $b->supplier_name,
                                'branch_id' => $b->branchName->branch_name ?? '',
                                'opening_balance' => number_format($b->opening_balance, 0, '.', ','),
                                'phone' => $b->phone,
                                'address' => $b->address,
                                'note' => $b->note,
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(b => [
                        b.supplier_name,
                        b.branch_id,
                        b.opening_balance,
                        b.phone,
                        b.address,
                        b.note,
                        b.id,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المورد',
                                'الفرع',
                                'رصيد المورد',
                                'الهاتف',
                                'العنوان',
                                'ملاحظات',
                                'تسديد',
                                'تعديل',
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 6,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const detailsUrl =
                                        `${baseUrl}/suppliers/${id}/details`;
                                    return `
                                    <a href="${detailsUrl}" class="btn btn-sm btn-outline-success" title="تسديد الدفعات">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                        </svg>
                                        دفع
                                    </a>
                                    `;
                                },
                            },
                            {
                                select: 7,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const editUrl =
                                        `${baseUrl}/warehouse/${id}&edit_Supplier/edit`;
                                    return `
                                    <a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="تعديل المعلومات">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        تعديل
                                    </a>
                                    `;
                                },
                            }
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

                },
            }));
        });
    </script>



@endsection
