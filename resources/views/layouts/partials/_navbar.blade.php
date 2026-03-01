{{-- Navbar --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <!-- Sidebar Toggle -->
        <button class="btn btn-link sidebar-toggle d-lg-none" type="button" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- Brand (visible on mobile) -->
        <a class="navbar-brand d-lg-none" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="32">
        </a>

        <!-- Search (optional) -->
        <div class="d-none d-lg-flex flex-grow-1">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="بحث..." id="globalSearch">
            </div>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav ms-auto align-items-center">
            <!-- Dark Mode Toggle -->
            <div class="nav-item">
                <button class="nav-link btn btn-link" id="darkModeToggle" title="تبديل الوضع">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <!-- Notifications -->
            <div class="nav-item dropdown">
                <a class="nav-link position-relative" href="#" id="notificationDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="badge bg-danger notification-badge" id="notificationCount">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                    aria-labelledby="notificationDropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>الإشعارات</span>
                        <a href="{{ route('notifications.index') }}" class="small">عرض الكل</a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div id="notificationList" class="notification-list">
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-bell-slash"></i>
                            <p class="mb-0 small">لا توجد إشعارات جديدة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="nav-item dropdown ms-3">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar me-2">
                        @if (auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" class="rounded-circle" width="36"
                                height="36">
                        @else
                            <div class="avatar-placeholder rounded-circle">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="d-none d-lg-block text-start">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <small class="text-muted">{{ auth()->user()->role_label ?? 'مستخدم' }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <strong>{{ auth()->user()->name }}</strong>
                        <br>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') ?? '#' }}">
                            <i class="fas fa-user me-2"></i> الملف الشخصي
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('notifications.settings.index') ?? '#' }}">
                            <i class="fas fa-cog me-2"></i> الإعدادات
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
