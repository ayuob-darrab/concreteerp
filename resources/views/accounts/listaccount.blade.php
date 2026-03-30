@extends('layouts.app')

@section('page-title', 'المستخدمين في شركة : ' . ($users->first()?->CompanyName?->name ?? ''))

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            {{-- رأس الصفحة مع زر الإضافة --}}
            <div class="mb-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    المستخدمون في شركة {{ $users->first()?->CompanyName?->name ?? '' }}
                </h5>

                <div class="flex items-center gap-3">
                    @if (!isset($canAddMore) || $canAddMore)
                        <a href="{{ url('accounts/GoToAddUser') }}">
                            <button type="button" class="btn btn-primary flex items-center gap-2">
                                <i class="fas fa-user-plus"></i>
                                <span>إضافة مستخدم جديد</span>
                            </button>
                        </a>
                    @else
                        <button type="button"
                            class="btn btn-secondary flex items-center gap-2 opacity-60 cursor-not-allowed" disabled>
                            <i class="fas fa-user-plus"></i>
                            <span>إضافة مستخدم جديد</span>
                        </button>
                        <span class="badge badge-outline-danger">يجب ترقية الاشتراك</span>
                    @endif
                </div>
            </div>

            {{-- تحذير: يجب تعطيل مستخدمين بسبب تقليل الاشتراك --}}
            @if (isset($needsDeactivation) && $needsDeactivation && isset($isCompanyManager) && $isCompanyManager)
                <div class="mb-5 p-4 rounded-lg border border-warning/50 bg-warning/10">
                    <div class="flex items-start gap-3">
                        <div class="rounded-full p-2 bg-warning/20 mt-1">
                            <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h6 class="font-bold text-warning text-lg mb-2">⚠️ تنبيه: يجب تعطيل {{ $excessUsers }}
                                حساب/حسابات</h6>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                الاشتراك الحالي يسمح بـ <strong class="text-primary">{{ $usersLimit }}</strong> مستخدمين
                                فقط،
                                ولكن لديك <strong class="text-danger">{{ $activeUsersCount }}</strong> مستخدمين نشطين.
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <strong>يرجى تعطيل {{ $excessUsers }} حساب/حسابات</strong> من القائمة أدناه للالتزام بحدود
                                الاشتراك.
                            </p>
                            <div class="text-xs text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 p-2 rounded">
                                <i class="fas fa-info-circle ml-1"></i>
                                <strong>ملاحظة:</strong> الحسابات المعطلة بسبب الاشتراك لن تُحذف، ويمكن إعادة تفعيلها عند
                                زيادة عدد المستخدمين في الاشتراك من قبل الإدارة.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- إشعار: يمكنك تفعيل مستخدمين معطلين (تم زيادة الاشتراك) --}}
            @if (isset($canActivateMore) &&
                    $canActivateMore &&
                    isset($isCompanyManager) &&
                    $isCompanyManager &&
                    !isset($needsDeactivation))
                <div class="mb-5 p-4 rounded-lg border border-success/50 bg-success/10">
                    <div class="flex items-start gap-3">
                        <div class="rounded-full p-2 bg-success/20 mt-1">
                            <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h6 class="font-bold text-success text-lg mb-2">✅ يمكنك إعادة تفعيل حسابات</h6>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                تم زيادة عدد المستخدمين في الاشتراك. يمكنك الآن إعادة تفعيل الحسابات المعطلة سابقاً.
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                انقر على زر <span class="badge badge-success">✓</span> الأخضر بجانب المستخدم لإعادة تفعيله.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- عرض معلومات حد المستخدمين --}}
            @if (isset($usersLimit) && $usersLimit)
                <div
                    class="mb-5 p-4 rounded-lg border {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'border-info/30 bg-info/5' : 'border-danger/30 bg-danger/5') : 'border-warning/30 bg-warning/5' }}">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded-full p-2 {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'bg-info/20' : 'bg-danger/20') : 'bg-warning/20' }}">
                                <svg class="h-5 w-5 {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'text-info' : 'text-danger') : 'text-warning' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h6
                                    class="font-semibold text-sm {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'text-info' : 'text-danger') : 'text-warning' }}">
                                    حد
                                    المستخدمين في الاشتراك</h6>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    مستخدمين نشطين: <strong>{{ $activeUsersCount ?? $currentUsersCount }}</strong> من أصل
                                    <strong>{{ $usersLimit }}</strong> مستخدم مسموح
                                    @if (isset($needsDeactivation) && $needsDeactivation)
                                        <span class="text-warning font-bold">- يجب تعطيل {{ $excessUsers }}!</span>
                                    @elseif ($canAddMore)
                                        <span class="text-success">(متبقي
                                            {{ $usersLimit - ($activeUsersCount ?? $currentUsersCount) }})</span>
                                    @else
                                        <span class="text-danger font-bold">- تم الوصول للحد الأقصى!</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm font-semibold {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'text-info' : 'text-danger') : 'text-warning' }}">
                                {{ $activeUsersCount ?? $currentUsersCount }}/{{ $usersLimit }}
                            </span>
                            <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full {{ !isset($needsDeactivation) || !$needsDeactivation ? ($canAddMore ? 'bg-info' : 'bg-danger') : 'bg-warning' }} rounded-full transition-all"
                                    style="width: {{ min(100, (($activeUsersCount ?? $currentUsersCount) / $usersLimit) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <table id="myTable2" class="whitespace-nowrap"></table>

        </div>



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

                    @php
                        $now = now();
                    @endphp
                    const tableData = {!! json_encode(
                        $users->map(function ($b) use ($now) {
                            $deactivatedAt = $b->subscription_deactivated_at ?? null;
                            $deadline = $deactivatedAt ? $deactivatedAt->copy()->addHours(48) : null;
                            $blocked48h = $deadline && $deadline->gt($now);
                            $hoursLeft = $blocked48h ? max(1, (int) ceil($deadline->diffInMinutes($now, false) / 60)) : 0;
                            return [
                                'id' => $b->id,
                                'fullname' => $b->fullname ?? '',
                                'branch_name' => $b->BranchName->branch_name ?? '',
                                'email' => $b->email,
                                'AccountType' => $b->AccountType->typename ?? '',
                                'usertype_id' => $b->Usertype->name ?? '',
                                'emp_type_id' => $b->Employeetype->name ?? '',
                                'is_active' => $b->is_active ? 'مفعل' : 'معطل',
                                'created_at' => \Carbon\Carbon::parse($b->created_at)->format('d-m-Y'),
                                'deactivated_by_subscription' => $b->deactivated_by_subscription ?? false,
                                'is_active_bool' => $b->is_active,
                                'reactivation_blocked_48h' => $blocked48h,
                                'hours_until_reactivate' => $hoursLeft,
                            ];
                        }),
                    ) !!};

                    const needsDeactivation =
                        {{ isset($needsDeactivation) && $needsDeactivation ? 'true' : 'false' }};
                    const isCompanyManager =
                        {{ isset($isCompanyManager) && $isCompanyManager ? 'true' : 'false' }};
                    const isSuperAdmin =
                        {{ isset($isSuperAdmin) && $isSuperAdmin ? 'true' : 'false' }};
                    const currentUserId = {{ auth()->id() }};

                    // هل يمكن تفعيل مستخدمين؟ (عدد النشطين أقل من الحد)
                    const canActivateMore =
                        {{ isset($canActivateMore) && $canActivateMore ? 'true' : 'false' }};

                    function escapeAttr(s) {
                        return String(s)
                            .replace(/&/g, '&amp;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#39;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');
                    }

                    const rows = tableData.map(b => {
                        // تحديد إذا كان المستخدم معطل لتلوين الصف
                        const isDeactivated = b.deactivated_by_subscription || !b
                            .is_active_bool;
                        const rowClass = isDeactivated ? 'deactivated-row' : '';

                        // أيقونة تعديل
                        const editHtml = `
                <a href="{{ url('accounts/${b.id}&editUserInformation/edit') }}"
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

                        // اسم المستخدم مع تمييز إذا كان معطل
                        let fullnameHtml = b.fullname;
                        if (b.deactivated_by_subscription) {
                            fullnameHtml =
                                `<span class="text-warning font-semibold">${escapeAttr(b.fullname)} <span class="badge badge-outline-warning text-xs mr-1">معطل</span></span>`;
                        } else if (!b.is_active_bool) {
                            fullnameHtml =
                                `<span class="text-danger">${escapeAttr(b.fullname)}</span>`;
                        }

                        // زر التعطيل/التفعيل بسبب الاشتراك
                        let subscriptionActionHtml = '';

                        // لا يمكن تعطيل المستخدم الحالي
                        if (b.id === currentUserId) {
                            subscriptionActionHtml =
                                `<span class="text-gray-400 text-xs">حسابك</span>`;
                        }
                        // إذا كان المستخدم معطل بسبب الاشتراك
                        else if (b.deactivated_by_subscription) {
                            // لا يمكن التفعيل قبل مرور 48 ساعة من التعطيل
                            if (b.reactivation_blocked_48h && b.hours_until_reactivate) {
                                subscriptionActionHtml =
                                    `<span class="badge badge-outline-secondary text-xs" title="لا يمكن إعادة التفعيل قبل 48 ساعة">بعد ${b.hours_until_reactivate} س</span>`;
                            }
                            // السوبر أدمن يمكنه التفعيل بعد 48 ساعة
                            else if (isSuperAdmin) {
                                subscriptionActionHtml = `
                                <button type="button" class="reactivate-subscription-btn btn btn-sm btn-success px-2 py-1"
                                        data-id="${b.id}" data-name="${escapeAttr(b.fullname)}" title="إعادة تفعيل الحساب">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <polyline points="16 11 18 13 22 9"/>
                                    </svg>
                                </button>`;
                            }
                            // مدير الشركة يمكنه التفعيل فقط إذا كان هناك مجال وبعد 48 ساعة
                            else if (isCompanyManager && canActivateMore) {
                                subscriptionActionHtml = `
                                <button type="button" class="reactivate-subscription-btn btn btn-sm btn-success px-2 py-1"
                                        data-id="${b.id}" data-name="${escapeAttr(b.fullname)}" title="إعادة تفعيل الحساب">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <polyline points="16 11 18 13 22 9"/>
                                    </svg>
                                </button>`;
                            }
                            // لا يمكن التفعيل - عرض badge فقط
                            else {
                                subscriptionActionHtml =
                                    `<span class="badge badge-outline-warning text-xs">معطل (اشتراك)</span>`;
                            }
                        }
                        // إذا كان هناك حاجة لتعطيل مستخدمين وهذا المستخدم نشط
                        else if (needsDeactivation && isCompanyManager && b.is_active_bool) {
                            subscriptionActionHtml = `
                            <button type="button" class="deactivate-subscription-btn btn btn-sm btn-danger px-2 py-1"
                                    data-id="${b.id}" data-name="${escapeAttr(b.fullname)}" title="تعطيل بسبب الاشتراك">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <line x1="17" y1="8" x2="23" y2="14"/>
                                    <line x1="23" y1="8" x2="17" y2="14"/>
                                </svg>
                            </button>`;
                        }
                        // السوبر أدمن يمكنه إعادة التفعيل لأي مستخدم معطل
                        else if (isSuperAdmin && !b.is_active_bool) {
                            subscriptionActionHtml = `
                            <button type="button" class="reactivate-subscription-btn btn btn-sm btn-success px-2 py-1"
                                    data-id="${b.id}" data-name="${escapeAttr(b.fullname)}" title="إعادة تفعيل الحساب">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <polyline points="16 11 18 13 22 9"/>
                                </svg>
                            </button>`;
                        }

                        return [
                            fullnameHtml,
                            b.branch_name,
                            b.email,
                            b.AccountType,
                            b.usertype_id,
                            b.emp_type_id,
                            b.deactivated_by_subscription ?
                            '<span class="badge badge-outline-warning">معطل (اشتراك)</span>' :
                            (b.is_active_bool ?
                                '<span class="badge badge-outline-success">مفعل</span>' :
                                '<span class="badge badge-outline-danger">معطل</span>'),
                            b.created_at,
                            editHtml,
                            resetHtml,
                            subscriptionActionHtml
                        ];
                    });

                    // حفظ معلومات المستخدمين المعطلين للتلوين
                    const deactivatedIds = tableData.filter(b => b.deactivated_by_subscription || !b
                        .is_active_bool).map(b => b.id);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الثلاثي',
                                'الاسم الفرع',
                                'الايميل',
                                'نوع الحساب',
                                'صلاحيات المستخدم',
                                'نوع المستخدم',
                                'نشط',
                                'تاريخ الإنشاء',
                                'تعديل',
                                'إعادة كلمة المرور',
                                'إجراء الاشتراك'
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
                            {
                                select: 8,
                                sortable: false
                            },
                            {
                                select: 9,
                                sortable: false
                            },
                            {
                                select: 10,
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

                    // تلوين صفوف المستخدمين المعطلين
                    function highlightDeactivatedRows() {
                        const table = document.querySelector('#myTable2');
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            const statusCell = row.querySelector('td:nth-child(7)');
                            if (statusCell) {
                                const hasWarningBadge = statusCell.querySelector(
                                    '.badge-outline-warning');
                                const hasDangerBadge = statusCell.querySelector(
                                    '.badge-outline-danger');
                                if (hasWarningBadge) {
                                    row.classList.add('bg-warning/20', 'dark:bg-warning/10');
                                    row.style.backgroundColor = 'rgba(255, 193, 7, 0.15)';
                                } else if (hasDangerBadge) {
                                    row.classList.add('bg-danger/20', 'dark:bg-danger/10');
                                    row.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
                                }
                            }
                        });
                    }

                    // تطبيق التلوين عند تحميل الجدول وعند تغيير الصفحة
                    this.datatable2.on('datatable.init', highlightDeactivatedRows);
                    this.datatable2.on('datatable.page', highlightDeactivatedRows);
                    this.datatable2.on('datatable.search', highlightDeactivatedRows);
                    this.datatable2.on('datatable.sort', highlightDeactivatedRows);
                    setTimeout(highlightDeactivatedRows, 100);

                    const self = this;
                    document.querySelector('#myTable2').addEventListener('click', function(e) {
                        const btn = e.target.closest('.reset-password-btn');
                        if (!btn) return;
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        self.open(name, id);
                    });

                    // معالج زر التعطيل بسبب الاشتراك
                    document.querySelector('#myTable2').addEventListener('click', function(e) {
                        const btn = e.target.closest('.deactivate-subscription-btn');
                        if (!btn) return;
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');

                        if (confirm(
                                `هل أنت متأكد من تعطيل حساب "${name}"؟\n\nملاحظة: لن يتمكن هذا المستخدم من تسجيل الدخول، ولإعادة تفعيله يجب التواصل مع الإدارة لزيادة عدد المستخدمين في الاشتراك.`
                            )) {
                            // إنشاء نموذج وإرساله
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `${baseUrl}/accounts/${id}/deactivate-subscription`;

                            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });

                    // معالج زر إعادة التفعيل (للسوبر أدمن فقط)
                    document.querySelector('#myTable2').addEventListener('click', function(e) {
                        const btn = e.target.closest('.reactivate-subscription-btn');
                        if (!btn) return;
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');

                        if (confirm(`هل أنت متأكد من إعادة تفعيل حساب "${name}"؟`)) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `${baseUrl}/accounts/${id}/reactivate-subscription`;

                            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                },
            }));
        });
    </script>
@endsection
