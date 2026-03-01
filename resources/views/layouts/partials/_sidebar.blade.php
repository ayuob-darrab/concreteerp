{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-brand">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo">
            <span class="sidebar-brand-text">ConcreteERP</span>
        </a>
        <button class="btn btn-link sidebar-close d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Company Info -->
    @if (auth()->user()->company ?? false)
        <div class="sidebar-company">
            <div class="company-avatar">
                {{ substr(auth()->user()->company->name ?? 'C', 0, 1) }}
            </div>
            <div class="company-info">
                <h6>{{ auth()->user()->company->name ?? 'الشركة' }}</h6>
                <small>{{ auth()->user()->branch->name ?? 'الفرع' }}</small>
            </div>
        </div>
    @endif

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- الرئيسية -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') || request()->is('/') ? 'active' : '' }}"
                    href="{{ url('/') }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li>

            <!-- الطلبات -->
            <li class="nav-item has-submenu {{ request()->routeIs('orders.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#ordersMenu">
                    <i class="fas fa-clipboard-list"></i>
                    <span>الطلبات</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('orders.*') ? 'show' : '' }}" id="ordersMenu">
                    <li><a href="{{ route('orders.create') ?? '#' }}">طلب جديد</a></li>
                    <li><a href="{{ route('orders.index') ?? '#' }}">جميع الطلبات</a></li>
                    <li><a href="{{ route('orders.pending') ?? '#' }}">المعلقة</a></li>
                </ul>
            </li>

            <!-- أوامر العمل -->
            <li class="nav-item has-submenu {{ request()->routeIs('work-jobs.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#workMenu">
                    <i class="fas fa-hard-hat"></i>
                    <span>أوامر العمل</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('work-jobs.*') ? 'show' : '' }}" id="workMenu">
                    <li><a href="{{ route('work-jobs.index') ?? '#' }}">جميع الأوامر</a></li>
                    <li><a href="{{ route('shipments.index') ?? '#' }}">الشحنات</a></li>
                </ul>
            </li>

            <!-- المخزون -->
            <li
                class="nav-item has-submenu {{ request()->routeIs('materials.*') || request()->routeIs('warehouse.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#inventoryMenu">
                    <i class="fas fa-warehouse"></i>
                    <span>المخزون</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('materials.*') ? 'show' : '' }}" id="inventoryMenu">
                    <li><a href="{{ route('materials.index') ?? '#' }}">المواد</a></li>
                    <li><a href="{{ route('warehouse.index') ?? '#' }}">حركات المستودع</a></li>
                </ul>
            </li>

            <!-- المالية -->
            <li
                class="nav-item has-submenu {{ request()->routeIs('receipts.*') || request()->routeIs('vouchers.*') || request()->routeIs('advances.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#financeMenu">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>المالية</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('receipts.*') || request()->routeIs('vouchers.*') ? 'show' : '' }}"
                    id="financeMenu">
                    <li><a href="{{ route('receipts.index') ?? '#' }}">إيصالات القبض</a></li>
                    <li><a href="{{ route('vouchers.index') ?? '#' }}">سندات الصرف</a></li>
                    <li><a href="{{ route('advances.index') ?? '#' }}">السلف</a></li>
                    <li><a href="{{ route('statements.index') ?? '#' }}">كشوف الحسابات</a></li>
                    <li><a href="{{ route('cash.daily') ?? '#' }}">الصندوق</a></li>
                </ul>
            </li>

            <!-- الموارد البشرية -->
            <li
                class="nav-item has-submenu {{ request()->routeIs('Employees.*') || request()->routeIs('payroll.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#hrMenu">
                    <i class="fas fa-users"></i>
                    <span>الموارد البشرية</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('Employees.*') ? 'show' : '' }}" id="hrMenu">
                    <li><a href="{{ route('Employees.index') ?? '#' }}">الموظفين</a></li>
                    <li><a href="{{ route('payroll.index') ?? '#' }}">الرواتب</a></li>
                </ul>
            </li>

            <!-- الآليات -->
            <li
                class="nav-item has-submenu {{ request()->routeIs('cars.*') || request()->routeIs('maintenance.*') ? 'open' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#vehiclesMenu">
                    <i class="fas fa-truck"></i>
                    <span>الآليات</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="collapse submenu {{ request()->routeIs('cars.*') ? 'show' : '' }}" id="vehiclesMenu">
                    <li><a href="{{ route('cars.index') ?? '#' }}">قائمة الآليات</a></li>
                    <li><a href="{{ route('maintenance.index') ?? '#' }}">الصيانة</a></li>
                </ul>
            </li>

            <!-- المقاولين -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contractors.*') ? 'active' : '' }}"
                    href="{{ route('contractors.index') ?? '#' }}">
                    <i class="fas fa-building"></i>
                    <span>المقاولين</span>
                </a>
            </li>

            <!-- التقارير -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                    href="{{ route('reports.index') ?? '#' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>التقارير</span>
                </a>
            </li>

            <!-- الإعدادات (للمدراء) -->
            @if (auth()->user()->role === 'super_admin' || auth()->user()->role === 'company_admin')
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#settingsMenu">
                        <i class="fas fa-cog"></i>
                        <span>الإعدادات</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="collapse submenu" id="settingsMenu">
                        <li><a href="{{ route('companyBranch.index') ?? '#' }}">الفروع</a></li>
                        <li><a href="{{ route('accounts.index') ?? '#' }}">الحسابات</a></li>
                        <li><a href="{{ route('pricing-categories.index') ?? '#' }}">فئات التسعير</a></li>
                    </ul>
                </li>
            @endif

            <!-- إدارة النظام (سوبر أدمن) -->
            @if (auth()->user()->role === 'super_admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                        href="{{ route('admin.statistics') ?? '#' }}">
                        <i class="fas fa-shield-alt"></i>
                        <span>إدارة النظام</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <small>الإصدار 1.0.0</small>
        <br>
        <small class="text-muted">&copy; {{ date('Y') }} ConcreteERP</small>
    </div>
</aside>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
