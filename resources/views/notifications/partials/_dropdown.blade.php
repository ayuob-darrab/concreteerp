<!-- قائمة الإشعارات المنسدلة للـ Navbar -->
<li class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="display: none;">
            0
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end notification-dropdown"
        style="width: 350px; max-height: 400px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-bell me-1"></i> الإشعارات</span>
            <a href="#" class="btn btn-sm btn-link mark-all-read" style="display: none;">
                تحديد الكل كمقروء
            </a>
        </div>
        <div class="notification-list">
            <!-- Loading -->
            <div class="notification-loading text-center py-4">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <!-- Empty -->
            <div class="notification-empty text-center py-4" style="display: none;">
                <i class="fas fa-bell-slash text-muted mb-2"></i>
                <p class="text-muted mb-0">لا توجد إشعارات جديدة</p>
            </div>
            <!-- Items container -->
            <div class="notification-items"></div>
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
            عرض جميع الإشعارات
        </a>
    </div>
</li>

<style>
    .notification-dropdown .dropdown-header {
        font-weight: bold;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }

    .notification-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.2s;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #e3f2fd;
    }

    .notification-item .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-item .notification-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .notification-item .notification-body {
        font-size: 13px;
        color: #666;
        margin-bottom: 2px;
    }

    .notification-item .notification-time {
        font-size: 11px;
        color: #999;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('notificationDropdown');
        const badge = document.querySelector('.notification-badge');
        const list = document.querySelector('.notification-items');
        const loading = document.querySelector('.notification-loading');
        const empty = document.querySelector('.notification-empty');
        const markAllBtn = document.querySelector('.mark-all-read');

        // تحميل الإشعارات عند فتح القائمة
        dropdown.addEventListener('show.bs.dropdown', loadNotifications);

        // تحديث العدد كل 30 ثانية
        setInterval(updateBadgeCount, 30000);
        updateBadgeCount();

        async function loadNotifications() {
            loading.style.display = 'block';
            empty.style.display = 'none';
            list.innerHTML = '';

            try {
                const response = await fetch('/notifications/dropdown');
                const data = await response.json();

                loading.style.display = 'none';

                if (data.notifications.length === 0) {
                    empty.style.display = 'block';
                    markAllBtn.style.display = 'none';
                    return;
                }

                markAllBtn.style.display = 'block';

                data.notifications.forEach(notification => {
                    const item = createNotificationItem(notification);
                    list.appendChild(item);
                });

            } catch (error) {
                loading.style.display = 'none';
                list.innerHTML = '<div class="text-center py-3 text-danger">حدث خطأ</div>';
            }
        }

        async function updateBadgeCount() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();

                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            } catch (error) {
                console.error('Error updating notification count:', error);
            }
        }

        function createNotificationItem(notification) {
            const div = document.createElement('a');
            div.href = notification.action_url || `/notifications/${notification.id}/mark-read`;
            div.className = 'notification-item d-flex unread text-decoration-none';

            const iconClass = getIconClass(notification.priority);
            const data = notification.data || {};

            div.innerHTML = `
            <div class="notification-icon bg-${iconClass} bg-opacity-10 me-3">
                <i class="fas fa-${notification.icon || 'bell'} text-${iconClass}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="notification-title">${data.title || 'إشعار'}</div>
                <div class="notification-body">${truncate(data.body || '', 50)}</div>
                <div class="notification-time">${timeAgo(notification.created_at)}</div>
            </div>
        `;

            return div;
        }

        function getIconClass(priority) {
            const classes = {
                'urgent': 'danger',
                'high': 'warning',
                'normal': 'primary',
                'low': 'secondary'
            };
            return classes[priority] || 'primary';
        }

        function truncate(str, len) {
            return str.length > len ? str.substring(0, len) + '...' : str;
        }

        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return 'الآن';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' دقيقة';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' ساعة';
            return Math.floor(seconds / 86400) + ' يوم';
        }

        // تحديد الكل كمقروء
        markAllBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    }
                });

                if (response.ok) {
                    badge.style.display = 'none';
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    markAllBtn.style.display = 'none';
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        });
    });
</script>
