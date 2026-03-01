{{-- قائمة المستخدم المنسدلة --}}
<div class="user-menu-dropdown">
    <div class="user-info px-3 py-2">
        <div class="d-flex align-items-center">
            <div class="avatar me-3">
                @if (auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="rounded-circle" width="48" height="48">
                @else
                    <div class="avatar-placeholder avatar-lg rounded-circle">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <small class="text-muted">{{ auth()->user()->email }}</small>
            </div>
        </div>
    </div>

    <hr class="dropdown-divider my-2">

    <a class="dropdown-item" href="{{ route('profile') ?? '#' }}">
        <i class="fas fa-user fa-fw me-2"></i>
        الملف الشخصي
    </a>

    <a class="dropdown-item" href="{{ route('notifications.settings.index') ?? '#' }}">
        <i class="fas fa-bell fa-fw me-2"></i>
        إعدادات الإشعارات
    </a>

    <a class="dropdown-item" href="#">
        <i class="fas fa-key fa-fw me-2"></i>
        تغيير كلمة المرور
    </a>

    <hr class="dropdown-divider my-2">

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt fa-fw me-2"></i>
            تسجيل الخروج
        </button>
    </form>
</div>
