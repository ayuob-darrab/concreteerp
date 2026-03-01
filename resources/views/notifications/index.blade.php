@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                <i class="fas fa-bell me-2"></i>
                الإشعارات
                @if ($unreadCount > 0)
                    <span class="badge bg-danger">{{ $unreadCount }} جديد</span>
                @endif
            </h4>
            <div>
                <a href="{{ route('notifications.settings') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-cog"></i> الإعدادات
                </a>
                @if ($unreadCount > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-double"></i> تحديد الكل كمقروء
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروء</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروء</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="new_order" {{ request('type') == 'new_order' ? 'selected' : '' }}>طلب جديد
                            </option>
                            <option value="order_offer_sent" {{ request('type') == 'order_offer_sent' ? 'selected' : '' }}>
                                عرض سعر</option>
                            <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>
                                استلام دفعة</option>
                            <option value="work_started" {{ request('type') == 'work_started' ? 'selected' : '' }}>بدء العمل
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select">
                            <option value="">الكل</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>مرتفع</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>عادي</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i></button>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-undo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- قائمة الإشعارات -->
        <div class="card">
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $data = $notification->data;
                    @endphp
                    <div class="notification-item p-3 border-bottom {{ $isUnread ? 'bg-light' : '' }}">
                        <div class="d-flex">
                            <div class="notification-icon me-3">
                                <div
                                    class="rounded-circle bg-{{ $notification->priority == 'urgent' ? 'danger' : ($notification->priority == 'high' ? 'warning' : 'primary') }} bg-opacity-10 p-3">
                                    <i
                                        class="fas fa-{{ $notification->icon ?? 'bell' }} text-{{ $notification->priority == 'urgent' ? 'danger' : ($notification->priority == 'high' ? 'warning' : 'primary') }}"></i>
                                </div>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                            {{ $data['title'] ?? 'إشعار' }}
                                            @if ($isUnread)
                                                <span class="badge bg-primary rounded-pill">جديد</span>
                                            @endif
                                        </h6>
                                        <p class="text-muted mb-2">{{ $data['body'] ?? '' }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="notification-actions">
                                        @if ($notification->action_url)
                                            <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif($isUnread)
                                            <form action="{{ route('notifications.mark-read', $notification->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    title="تحديد كمقروء">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('notifications.destroy', $notification->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"
                                                onclick="return confirm('حذف الإشعار؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد إشعارات</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{ $notifications->links() }}
    </div>
@endsection
