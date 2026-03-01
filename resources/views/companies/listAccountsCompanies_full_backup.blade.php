@extends('layouts.app')

@section('page-title', 'تحديث حساب الشركات')

@section('content')
    <style>
        /* بطاقات الإحصائيات */
        .stats-card {
            background: linear-gradient(135deg, var(--start), var(--end));
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.2);
        }

        .stats-card.primary {
            --start: #4f46e5;
            --end: #6366f1;
        }

        .stats-card.success {
            --start: #10b981;
            --end: #059669;
        }

        .stats-card.danger {
            --start: #ef4444;
            --end: #dc2626;
        }

        .dark .stats-card {
            background: linear-gradient(135deg, rgba(75, 85, 99, 0.9), rgba(55, 65, 81, 0.9));
        }

        .stats-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            background: rgba(255, 255, 255, 0.2);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .status-active {
            background-color: #10b981;
            color: white;
        }

        .status-inactive {
            background-color: #f59e0b;
            color: white;
        }

        /* Center modal better */
        .modal-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .stats-card {
                padding: 1rem;
            }

            .stats-icon {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }

            .stats-number {
                font-size: 1.5rem;
            }
        }
    </style>

    <div x-data="multipleTable">
        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="stats-card primary text-white">
                <div class="flex items-center gap-4">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="flex-1">
                        <div class="stats-label">إجمالي الحسابات</div>
                        <div class="stats-number">{{ isset($stats['total']) ? $stats['total'] : 0 }}</div>
                        <div class="stats-label opacity-75">حساب مسجل</div>
                    </div>
                </div>
            </div>

            <div class="stats-card success text-white">
                <div class="flex items-center gap-4">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="flex-1">
                        <div class="stats-label">الحسابات النشطة</div>
                        <div class="stats-number">{{ isset($stats['active']) ? $stats['active'] : 0 }}</div>
                        <div class="stats-label opacity-75">
                            {{ isset($stats['total']) && $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0 }}%
                            من
                            الإجمالي
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-card danger text-white">
                <div class="flex items-center gap-4">
                    <div class="stats-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="flex-1">
                        <div class="stats-label">الحسابات المعطلة</div>
                        <div class="stats-number">{{ isset($stats['inactive']) ? $stats['inactive'] : 0 }}</div>
                        <div class="stats-label opacity-75">
                            {{ isset($stats['total']) && $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100, 1) : 0 }}%
                            من
                            الإجمالي
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">

                <a href="/ConcreteERP/companies/NewAccountsCompany" class="btn btn-primary flex items-center gap-2">
                    <i class="fas fa-building"></i>
                    <span>إضافة شركة جديدة 🏢</span>
                </a>
            </h3>
            <table id="myTable2" class="whitespace-nowrap">
                <caption class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    حساب الشركات في البرنامج
                </caption>
            </table>

        </div>

        <!-- Modal -->
        <div class="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto" x-show="openModal" x-cloak>
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

                    <form :action="`/ConcreteERP/companies/${selectedUserId}`" method="POST" autocomplete="off">
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
        </div>
    </div>

    <script>
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
                    console.log('Starting init...');
                    let tableData;
                    try {
                        tableData = {!! json_encode(
                            $users->map(function ($b) {
                                // ✅ استخدام البيانات المحملة مسبقاً (Eager Loading)
                                $company = $b->CompanyName;
                                return [
                                    'id' => $b->id,
                                    'fullname' => $b->fullname,
                                    'email' => $b->email,
                                    'company_name' => $company ? $company->name : 'N/A',
                                    'company_code' => $company ? $company->code : null,
                                    'company_logo' => $company ? $company->logo : null,
                                    'is_active' => $b->is_active,
                                    'is_active_text' => $b->is_active ? 'مفعل' : 'معطل',
                                    'created_at' => \Carbon\Carbon::parse($b->created_at)->format('d-m-Y'),
                                ];
                            }),
                        ) !!};
                        console.log('Table data loaded:', tableData.length, 'rows');
                    } catch (e) {
                        console.error('Error loading table data:', e);
                        tableData = [];
                    }

                    if (!tableData || tableData.length === 0) {
                        console.warn('No data to display');
                        alert('لا توجد بيانات لعرضها');
                        return;
                    }

                    function escapeAttr(s) {
                        return String(s)
                            .replace(/&/g, '&amp;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#39;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');
                    }

                    const rows = tableData.map(b => {
                        // أيقونة تعديل
                        const editHtml = `
                <a href="/ConcreteERP/companies/${b.id}&editCompanyAccount/edit"
                   class="text-blue-600 hover:text-blue-800" title="تعديل">
                   <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-pencil">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z" />
                   </svg>
                </a>`;

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

                        // إعداد شارة الحالة باللون المناسب
                        const statusBadge = b.is_active ?
                            `<span class="status-badge status-active">${b.is_active_text}</span>` :
                            `<span class="status-badge status-inactive">${b.is_active_text}</span>`;

                        // ✅ عرض شعار الشركة
                        const logoHtml = b.company_logo && b.company_code ?
                            `<div class="flex items-center gap-2">
                                <img src="/ConcreteERP/public/uploads/${b.company_code}/companies_logo/${b.company_logo}" 
                                     alt="${escapeAttr(b.company_name || b.company_code)}" 
                                     class="w-8 h-8 rounded-full object-cover"
                                     onerror="this.src='/ConcreteERP/public/assets/images/default-company.png'">
                                <span>${b.company_name}</span>
                             </div>` :
                            b.company_name || 'N/A';

                        return [
                            b.fullname,
                            b.email,
                            logoHtml,
                            statusBadge,
                            b.created_at,
                            editHtml,
                            resetHtml
                        ];
                    });

                    console.log('Creating DataTable with', rows.length, 'rows');

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الثلاثي',
                                'الايميل',
                                'اسم الشركة',
                                'نشط',
                                'تاريخ الإنشاء',
                                'تعديل',
                                'إعادة كلمة المرور'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 5,
                                sortable: false
                            },
                            {
                                select: 6,
                                sortable: false
                            }
                        ],
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });

                    console.log('DataTable initialized successfully');

                    const self = this;
                    document.querySelector('#myTable2').addEventListener('click', function(e) {
                        const btn = e.target.closest('.reset-password-btn');
                        if (!btn) return;
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        self.open(name, id);
                    });

                    console.log('Init completed');
                },
            }));
        });
    </script>
@endsection
