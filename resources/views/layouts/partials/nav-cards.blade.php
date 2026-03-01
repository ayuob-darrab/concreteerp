{{-- عرض البطاقات بنفس صلاحيات السلايد بار - نفس الإشعارات والبادجات - ثيم لايت/دارك وتجاوب --}}
@php
    $cardClass = 'flex flex-col rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#0e1726] p-4 shadow-sm transition-all duration-200 hover:border-primary hover:shadow-md dark:hover:border-primary/50 min-h-[90px]';
    $cardContentClass = 'flex items-center justify-between gap-2 flex-wrap';
    $titleClass = 'font-semibold text-gray-800 dark:text-white-light';
    $gridClass = 'grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
    $sectionTitleClass = 'mb-3 text-base font-bold text-gray-700 dark:text-gray-200 border-b border-gray-200 dark:border-gray-600 pb-2';
    $badgeClass = 'badge shrink-0 rounded-full px-2 py-0.5 text-xs';
@endphp

<div class="space-y-8" dir="rtl">
    {{-- سوبر أدمن --}}
    @if (Auth::user()->usertype_id == 'SA' && Auth::user()->company_code == 'SA' && Auth::user()->account_code == 'SA')
        @if (Auth::user()->account_code != 'cont')
            <section>
                <h2 class="{{ $sectionTitleClass }}">الإدارة العليا</h2>
                <div class="{{ $gridClass }}">
                    <a href="/ConcreteERP/admin/super-admin-users" class="{{ $cardClass }}">إدارة حسابات السوبر أدمن</a>
                    <a href="/ConcreteERP/companies/ListCompanies" class="{{ $cardClass }}">إضافة شركة</a>
                    <a href="/ConcreteERP/companies/listAccountsCompanies" class="{{ $cardClass }}">حسابات الشركات</a>
                    <a href="/ConcreteERP/subscriptions/plans" class="{{ $cardClass }}">خطط الاشتراك</a>
                    <a href="/ConcreteERP/subscriptions/companies" class="{{ $cardClass }}">إدارة اشتراكات الشركات</a>
                    <a href="/ConcreteERP/subscriptions/settings" class="{{ $cardClass }}">إعدادات الأسعار</a>
                    <a href="/ConcreteERP/subscriptions/financial-reports" class="{{ $cardClass }}">التقارير المالية</a>
                    <a href="/ConcreteERP/subscriptions/monitor" class="{{ $cardClass }}">مراقبة الاشتراكات</a>
                    <a href="/ConcreteERP/payment-cards" class="{{ $cardClass }}">حسابات الدفع الإلكتروني</a>
                    <a href="/ConcreteERP/payment-cards-report/transactions" class="{{ $cardClass }}">تقرير المعاملات</a>
                    <a href="/ConcreteERP/admin/users" class="{{ $cardClass }}">جميع المستخدمين</a>
                    <a href="/ConcreteERP/admin/roles" class="{{ $cardClass }}">الأدوار والصلاحيات</a>
                    <a href="/ConcreteERP/admin/activity-logs" class="{{ $cardClass }}">سجلات النشاط</a>
                    <a href="/ConcreteERP/admin/statistics" class="{{ $cardClass }}">إحصائيات النظام</a>
                    <a href="/ConcreteERP/admin/performance" class="{{ $cardClass }}">تقارير الأداء</a>
                    <a href="/ConcreteERP/admin/settings" class="{{ $cardClass }}">الإعدادات العامة</a>
                    <a href="/ConcreteERP/admin/backups" class="{{ $cardClass }}">النسخ الاحتياطي</a>
                    <a href="/ConcreteERP/admin/notifications/list" class="{{ $cardClass }}">إدارة الإشعارات</a>
                    <a href="/ConcreteERP/admin/cities" class="{{ $cardClass }}">المدن والمناطق</a>
                    <a href="/ConcreteERP/admin/employee-types" class="{{ $cardClass }}">أنواع الموظفين</a>
                    <a href="/ConcreteERP/materials/listmeasurement_units" class="{{ $cardClass }}">وحدات القياس</a>
                    <a href="/ConcreteERP/materials/ConcreteMix" class="{{ $cardClass }}">أنواع الخرسانة</a>
                    <a href="/ConcreteERP/pricing-categories" class="{{ $cardClass }}">الفئات السعرية</a>
                    <a href="/ConcreteERP/admin/tickets" class="{{ $cardClass }}">تذاكر الدعم</a>
                    <a href="/ConcreteERP/admin/error-logs" class="{{ $cardClass }}">سجل الأخطاء</a>
                    <a href="/ConcreteERP/admin/system-health" class="{{ $cardClass }}">صحة النظام</a>
                </div>
            </section>
        @endif
    @endif

    {{-- مدير الشركة CM --}}
    @if (Auth::user()->account_code != 'cont')
        @if (Auth::user()->usertype_id == 'CM')
            @php
                $newNotificationsCount = \App\Models\Notification::where(function ($q) {
                    $q->where('company_code', Auth::user()->company_code)->orWhere('company_code', 'ALL');
                })->where('is_read', false)->count();
                $openTicketsCount = \App\Models\SupportTicket::where('company_code', Auth::user()->company_code)
                    ->whereIn('status', ['open', 'in_progress', 'pending_response'])->count();
            @endphp
            <section>
                <h2 class="{{ $sectionTitleClass }}">لوحة التحكم والشركة</h2>
                <div class="{{ $gridClass }}">
                    <a href="/ConcreteERP/home" class="{{ $cardClass }}"><span class="{{ $titleClass }}">لوحة التحكم</span></a>
                    <a href="{{ route('companyBranch.company.orders.dashboard') }}" class="{{ $cardClass }}"><span class="{{ $titleClass }}">الطلبات لكل الأفرع</span></a>
                    <a href="/ConcreteERP/companyBranch/Allbranch" class="{{ $cardClass }}">الأفرع</a>
                    <a href="/ConcreteERP/Employees/ListEmployees" class="{{ $cardClass }}">الموظفين</a>
                    <a href="/ConcreteERP/accounts/listaccount" class="{{ $cardClass }}">حسابات المستخدمين</a>
                    <a href="/ConcreteERP/companies/ShiftTimes" class="{{ $cardClass }}">شفتات العمل</a>
                    <a href="{{ route('attendance.admin.report') }}" class="{{ $cardClass }}">عرض الحضور لكل الفروع</a>
                    <a href="/ConcreteERP/contractors/List" class="{{ $cardClass }}">المقاولين</a>
                    <a href="/ConcreteERP/warehouse/addSupplier" class="{{ $cardClass }}">موردي المواد</a>
                    <a href="/ConcreteERP/warehouse/CompanyListConcreteMix" class="{{ $cardClass }}">الخرسانة</a>
                    <a href="/ConcreteERP/company-prices" class="{{ $cardClass }}">أسعار الفئات</a>
                    <a href="/ConcreteERP/warehouse/addMainMaterials" class="{{ $cardClass }}">المواد الأساسية</a>
                    <a href="/ConcreteERP/warehouse/listchemicals" class="{{ $cardClass }}">المواد الكيميائية</a>
                    <a href="/ConcreteERP/materials/listMaterialEquipment" class="{{ $cardClass }}">سعات المواد</a>
                    <a href="/ConcreteERP/car-types" class="{{ $cardClass }}">أنواع السيارات</a>
                    <a href="/ConcreteERP/cars/ListCar" class="{{ $cardClass }}">السيارات</a>
                    <a href="/ConcreteERP/company/notifications" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">إشعارات النظام</span>
                            @if ($newNotificationsCount > 0)
                                <span class="{{ $badgeClass }} bg-primary text-white">{{ $newNotificationsCount > 99 ? '99+' : $newNotificationsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/support" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">تذاكر الدعم</span>
                            @if ($openTicketsCount > 0)
                                <span class="{{ $badgeClass }} bg-warning text-white">{{ $openTicketsCount > 99 ? '99+' : $openTicketsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/company-payment-cards" class="{{ $cardClass }}">بطاقات الدفع</a>
                    <a href="/ConcreteERP/company-payment-cards-report/transactions" class="{{ $cardClass }}">تقرير المعاملات</a>
                    <a href="/ConcreteERP/branch/payments/report" class="{{ $cardClass }}">تقرير المقبوضات</a>
                    <a href="/ConcreteERP/branch/payments/branches-report" class="{{ $cardClass }}">تقرير الفروع</a>
                    <a href="{{ route('financial-report.index') }}" class="{{ $cardClass }}">تقرير الطلبات</a>
                    <a href="/ConcreteERP/financial/reports/daily" class="{{ $cardClass }}">التقرير اليومي</a>
                </div>
            </section>
        @endif

        {{-- مدير الفرع BM --}}
        @if (Auth::user()->usertype_id == 'BM')
            @php
                $newRequestOrdersCount = \App\Models\WorkOrder::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->where('status_code', 'new')->whereNull('branch_approval_status')->count();
                $approvedByContractorCount = \App\Models\WorkOrder::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->where('branch_approval_status', 'approved')->where('requester_approval_status', 'approved')
                    ->where('status_code', 'new')->count();
                $inProgressOrdersCount = \App\Models\WorkOrder::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)->where('status_code', 'in_progress')->count();
                $unpaidCustomersCount = \App\Models\WorkOrder::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->whereIn('status_code', ['in_progress', 'completed'])
                    ->where(function ($q) {
                        $q->where('payment_status', '!=', 'paid')->orWhereNull('payment_status');
                    })->distinct('customer_phone')->count('customer_phone');
                $pendingJobsCount = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->whereIn('status', ['pending', 'materials_reserved'])->count();
                $activeJobsCount = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)->where('status', 'in_progress')->count();
                $todayJobsCount = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id)->whereDate('scheduled_date', today())
                    ->whereIn('status', ['pending', 'materials_reserved', 'in_progress'])->count();
            @endphp
            <section>
                <h2 class="{{ $sectionTitleClass }}">لوحة التحكم والطلبات</h2>
                <div class="{{ $gridClass }}">
                    <a href="/ConcreteERP/home" class="{{ $cardClass }}"><span class="{{ $titleClass }}">لوحة التحكم</span></a>
                    <a href="/ConcreteERP/companyBranch/directRequest" class="{{ $cardClass }}">طلب مباشر</a>
                    <a href="/ConcreteERP/companyBranch/listNewRequestOrders" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">الطلبات الجديدة</span>
                            @if ($newRequestOrdersCount > 0)
                                <span class="{{ $badgeClass }} bg-danger text-white">{{ $newRequestOrdersCount > 99 ? '99+' : $newRequestOrdersCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/listApprovedByContractor" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">بانتظار الموافقة النهائية</span>
                            @if ($approvedByContractorCount > 0)
                                <span class="{{ $badgeClass }} bg-success text-white">{{ $approvedByContractorCount > 99 ? '99+' : $approvedByContractorCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/ordersInProgress" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">قيد العمل</span>
                            @if ($inProgressOrdersCount > 0)
                                <span class="{{ $badgeClass }} bg-warning text-dark">{{ $inProgressOrdersCount > 99 ? '99+' : $inProgressOrdersCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/ordersCompleted" class="{{ $cardClass }}">المكتملة</a>
                    <a href="/ConcreteERP/branch/payments" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">دفعات الزبائن</span>
                            @if ($unpaidCustomersCount > 0)
                                <span class="{{ $badgeClass }} bg-danger text-white">{{ $unpaidCustomersCount > 99 ? '99+' : $unpaidCustomersCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/branch/payments/report" class="{{ $cardClass }}">تقرير المقبوضات</a>
                    <a href="/ConcreteERP/companyBranch/execution/dashboard" class="{{ $cardClass }}">لوحة التحكم (التنفيذ)</a>
                    <a href="/ConcreteERP/companyBranch/workJobs/today" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">أعمال اليوم</span>
                            @if ($todayJobsCount > 0)
                                <span class="{{ $badgeClass }} bg-primary text-white">{{ $todayJobsCount > 99 ? '99+' : $todayJobsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/workJobs/pending" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">بانتظار التنفيذ</span>
                            @if ($pendingJobsCount > 0)
                                <span class="{{ $badgeClass }} bg-warning text-dark">{{ $pendingJobsCount > 99 ? '99+' : $pendingJobsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/workJobs/active" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">قيد التنفيذ</span>
                            @if ($activeJobsCount > 0)
                                <span class="{{ $badgeClass }} bg-info text-white">{{ $activeJobsCount > 99 ? '99+' : $activeJobsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/companyBranch/workJobs/completed" class="{{ $cardClass }}">المكتملة (أعمال)</a>
                    <a href="/ConcreteERP/companyBranch/workShipments" class="{{ $cardClass }}">الشحنات</a>
                    <a href="/ConcreteERP/companyBranch/BranchManage" class="{{ $cardClass }}">إعدادات الفرع</a>
                    <a href="/ConcreteERP/accounts/listBranchaccounts" class="{{ $cardClass }}">المستخدمين</a>
                    <a href="/ConcreteERP/Employees/listBranchemployees" class="{{ $cardClass }}">موظفين الفرع</a>
                    <a href="/ConcreteERP/attendance/admin/report" class="{{ $cardClass }}">تقرير الحضور</a>
                    <a href="/ConcreteERP/warehouse/BranchConcreteMix" class="{{ $cardClass }}">أنواع الخرسانة</a>
                    <a href="/ConcreteERP/warehouse/addMainMaterialsBranch" class="{{ $cardClass }}">المواد الأساسية</a>
                    <a href="/ConcreteERP/warehouse/Branchlistchemicals" class="{{ $cardClass }}">المواد الكيميائية</a>
                    <a href="/ConcreteERP/warehouse/addSupplier" class="{{ $cardClass }}">موردي المواد</a>
                    <a href="/ConcreteERP/car-maintenance" class="{{ $cardClass }}">صيانة السيارات</a>
                    <a href="/ConcreteERP/contractors/List" class="{{ $cardClass }}">المقاولين</a>
                </div>
            </section>
        @endif
    @endif

    {{-- المقاول --}}
    @if (Auth::user()->account_code == 'cont')
        @php
            $pendingApprovalCount = \App\Models\WorkOrder::where('sender_type', 'cont')
                ->where('sender_id', Auth::user()->id)
                ->where('branch_approval_status', 'approved')
                ->whereNull('requester_approval_status')->count();
        @endphp
        <section>
            <h2 class="{{ $sectionTitleClass }}">لوحة المقاول</h2>
            <div class="{{ $gridClass }}">
                <a href="{{ url('/home') }}" class="{{ $cardClass }}"><span class="{{ $titleClass }}">لوحة التحكم</span></a>
                <a href="/ConcreteERP/contractors/SendRequestsContractor" class="{{ $cardClass }}">تقديم طلب جديد</a>
                <a href="/ConcreteERP/contractors/MyPendingOrders" class="{{ $cardClass }}">طلباتي الجديدة</a>
                <a href="/ConcreteERP/contractors/CheckRequestsContractor" class="{{ $cardClass }}">
                    <span class="{{ $cardContentClass }}">
                        <span class="{{ $titleClass }}">بانتظار موافقتي</span>
                        @if ($pendingApprovalCount > 0)
                            <span class="{{ $badgeClass }} bg-danger text-white">{{ $pendingApprovalCount > 99 ? '99+' : $pendingApprovalCount }}</span>
                        @endif
                    </span>
                </a>
                <a href="/ConcreteERP/contractors/ApprovedOrders" class="{{ $cardClass }}">قيد العمل</a>
                <a href="{{ route('contractor-invoices.index') }}" class="{{ $cardClass }}">فواتير الطلبات</a>
            </div>
        </section>
    @endif

    {{-- الموظف emp --}}
    @if (Auth::user()->account_code == 'emp')
        @php
            $employee = \App\Models\Employee::where('user_id', Auth::user()->id)->first();
            $todayAttendance = $employee ? \App\Models\Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', \Carbon\Carbon::today())->first() : null;
            $driverShipmentsCount = $employee ? \App\Models\WorkShipment::where(function($q) use ($employee) {
                $q->where('mixer_driver_id', $employee->id)->orWhere('truck_driver_id', $employee->id)->orWhere('pump_driver_id', $employee->id);
            })->whereNotIn('status', ['returned', 'cancelled'])->count() : 0;
            $isDriver = $employee && $employee->job_title && (str_contains(strtolower($employee->job_title), 'سائق') || str_contains(strtolower($employee->job_title), 'driver'));
        @endphp
        <section>
            <h2 class="{{ $sectionTitleClass }}">الموظف</h2>
            <div class="{{ $gridClass }}">
                @if (Auth::user()->usertype_id != 'CM' && Auth::user()->usertype_id != 'BM')
                    <a href="/ConcreteERP/attendance" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">تسجيل الحضور</span>
                            @if (!$todayAttendance)
                                <span class="{{ $badgeClass }} bg-warning text-dark">لم تسجل</span>
                            @elseif (!$todayAttendance->check_out_time)
                                <span class="{{ $badgeClass }} bg-success text-white">حاضر</span>
                            @else
                                <span class="{{ $badgeClass }} bg-secondary text-white">مكتمل</span>
                            @endif
                        </span>
                    </a>
                    <a href="/ConcreteERP/attendance/my-history" class="{{ $cardClass }}">سجل الحضور</a>
                @endif
                @if ($driverShipmentsCount > 0 || $isDriver)
                    <a href="/ConcreteERP/driver/shipments" class="{{ $cardClass }}">
                        <span class="{{ $cardContentClass }}">
                            <span class="{{ $titleClass }}">شحناتي</span>
                            @if ($driverShipmentsCount > 0)
                                <span class="{{ $badgeClass }} bg-primary text-white">{{ $driverShipmentsCount > 99 ? '99+' : $driverShipmentsCount }}</span>
                            @endif
                        </span>
                    </a>
                @endif
            </div>
        </section>
    @endif
</div>
