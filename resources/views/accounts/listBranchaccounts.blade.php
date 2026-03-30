@extends('layouts.app')

@section('page-title', 'المستخدمين في الفرع : ' . ($users->first()?->BranchName?->branch_name ?? ''))

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">

            <table id="myTable2" class="whitespace-nowrap">
                <caption class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    المستخدمون في الفرع :
                    {{ $users->first()?->BranchName?->branch_name ?? '' }}
                </caption>
            </table>

        </div>



        <div class="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto" x-show="openModal" x-cloak>
            {{-- <div class="modal-container px-4" @click.self="cancelChange()"> --}}
            <div x-show="openModal" x-transition
                class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg mx-auto">

                <!-- Header -->
                <div class="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3 border-b">
                    <h5 class="font-bold text-lg text-xl">
                        تغيير كلمة المرور للمستخدم: <span x-text="selectedUserName"></span>
                    </h5>
                </div>

                <!-- Body -->
                <div class="p-5 text-center">
                    <h3 class="text-lg font-semibold mb-3">أدخل كلمة المرور الجديدة للمستخدم:</h3>

                    <form :action="`${baseUrl}/accounts/${selectedUserId}`" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <label for="newPassword"
                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                كلمة المرور الجديدة
                            </label>
                            <input id="newPassword" name="newPassword" type="text" x-model="newPassword"
                                class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary/30 dark:bg-gray-800 dark:text-white"
                                required>
                        </div>

                        <div class="flex justify-center gap-4">
                            <button type="button" class="btn btn-outline-danger" @click="cancelChange()">إلغاء</button>
                            <button type="submit" name="active" value="UpdatePassword" class="btn btn-primary">تغيير
                                كلمة المرور</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- </div> --}}
        </div>


    </div>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,
                openModal: false,
                selectedUserName: '',
                selectedUserId: null,
                newPassword: '',

                // فتح المودال وتعيين اسم ومعرف المستخدم
                open(name, id) {
                    this.selectedUserName = name;
                    this.selectedUserId = id;
                    this.newPassword = '';
                    this.openModal = true;
                },

                // الغاء العملية ومسح الحقل
                cancelChange() {
                    this.newPassword = '';
                    this.openModal = false;
                },

                init() {
                    const tableData = {!! json_encode(
                        $users->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'fullname' => $b->fullname,
                                'branch_name' => $b->BranchName ? $b->BranchName->branch_name : '-',
                                'username' => $b->username,
                                'usertype_id' => $b->Usertype ? $b->Usertype->name : '-',
                    
                                'emp_type_id' => $b->Employeetype ? $b->Employeetype->name : '-',
                                'is_active' => $b->is_active ? 'مفعل' : 'معطل',
                                'created_at' => \Carbon\Carbon::parse($b->created_at)->format('d-m-Y'),
                            ];
                        }),
                    ) !!};

                    function escapeAttr(s) {
                        return String(s)
                            .replace(/&/g, '&amp;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#39;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');
                    }

                    const rows = tableData.map(b => {


                        // أيقونة إعادة تعيين كلمة المرور  
                        const resetHtml = `
                <button type="button" class="reset-password-btn text-red-600 hover:text-red-800"
                        data-id="${b.id}" data-name="${escapeAttr(b.fullname)}" title="إعادة تعيين كلمة المرور">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-lock">
                         <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                         <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </button>`;

                        return [
                            b.fullname,
                            b.branch_name,
                            b.username,
                            b.usertype_id,
                            b.emp_type_id,
                            b.is_active,
                            b.created_at,

                            resetHtml
                        ];
                    });

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الثلاثي',
                                'الاسم الفرع',
                                'الايميل',
                                'صلاحيات المستخدم',
                                'نوع المستخدم',
                                'نشط',
                                'تاريخ الإنشاء',

                                'إعادة كلمة المرور'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 7,
                                sortable: false
                            },

                        ],
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });

                    const self = this;
                    document.querySelector('#myTable2').addEventListener('click', function(e) {
                        const btn = e.target.closest('.reset-password-btn');
                        if (!btn) return;
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        self.open(name, id);
                    });
                },
            }));
        });
    </script>
@endsection
