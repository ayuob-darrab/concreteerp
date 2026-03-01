@extends('layouts.app')

@section('title', 'تفاصيل التفاوض - طلب #' . $order->id)

@section('content')
    <div class="container-fluid">
        <!-- رأس الصفحة -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-handshake text-primary"></i>
                            تفاصيل التفاوض - طلب #{{ $order->id }}
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">الطلبات</a></li>
                                <li class="breadcrumb-item active">تفاوض #{{ $order->id }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- معلومات الطلب -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات الطلب</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">رقم الطلب:</th>
                                <td>#{{ $order->id }}</td>
                            </tr>
                            <tr>
                                <th>العميل:</th>
                                <td>{{ $order->customer_name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>الهاتف:</th>
                                <td>{{ $order->customer_phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>الكمية:</th>
                                <td>{{ number_format($order->quantity, 2) }} م³</td>
                            </tr>
                            <tr>
                                <th>نوع الخلطة:</th>
                                <td>{{ $order->concreteMix->name ?? $order->classification }}</td>
                            </tr>
                            <tr>
                                <th>الموقع:</th>
                                <td>{{ $order->location ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>تاريخ التسليم:</th>
                                <td>{{ $order->delivery_datetime ? $order->delivery_datetime->format('Y-m-d H:i') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>الحالة:</th>
                                <td>
                                    @php
                                        $statusColors = [
                                            'new' => 'secondary',
                                            'branch_approved' => 'info',
                                            'branch_rejected' => 'danger',
                                            'waiting_customer' => 'warning',
                                            'negotiation' => 'primary',
                                            'customer_approved' => 'success',
                                            'approved' => 'success',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                        $statusLabels = [
                                            'new' => 'جديد',
                                            'branch_approved' => 'موافق عليه من الفرع',
                                            'branch_rejected' => 'مرفوض من الفرع',
                                            'waiting_customer' => 'بانتظار رد العميل',
                                            'negotiation' => 'في التفاوض',
                                            'customer_approved' => 'موافق عليه من العميل',
                                            'approved' => 'معتمد',
                                            'in_progress' => 'قيد التنفيذ',
                                            'completed' => 'مكتمل',
                                            'cancelled' => 'ملغي',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$order->status_code] ?? 'secondary' }}">
                                        {{ $statusLabels[$order->status_code] ?? $order->status_code }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- الأسعار -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> الأسعار</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>السعر المبدئي:</th>
                                <td>{{ number_format($order->initial_price ?? 0, 2) }} د.ع</td>
                            </tr>
                            <tr>
                                <th>سعر الفرع:</th>
                                <td>{{ number_format($order->branch_price ?? 0, 2) }} د.ع</td>
                            </tr>
                            <tr>
                                <th>سعر العميل المقترح:</th>
                                <td>{{ number_format($order->requester_price ?? 0, 2) }} د.ع</td>
                            </tr>
                            <tr class="table-success">
                                <th>السعر النهائي:</th>
                                <td><strong>{{ number_format($order->final_price ?? 0, 2) }} د.ع</strong></td>
                            </tr>
                            <tr>
                                <th>إجمالي الطلب:</th>
                                <td><strong>{{ number_format(($order->final_price ?? ($order->initial_price ?? 0)) * $order->quantity, 2) }}
                                        د.ع</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- إجراءات سريعة -->
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                    </div>
                    <div class="card-body">
                        @if ($order->status_code == 'new' && !$order->branch_reviewed)
                            <button class="btn btn-success btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#branchReviewModal">
                                <i class="fas fa-check"></i> مراجعة الفرع
                            </button>
                        @endif

                        @if ($order->status_code == 'branch_approved' || $order->status_code == 'negotiation')
                            <button class="btn btn-primary btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#sendOfferModal">
                                <i class="fas fa-paper-plane"></i> إرسال عرض سعر
                            </button>
                        @endif

                        @if ($order->status_code == 'waiting_customer')
                            <button class="btn btn-success btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#acceptOfferModal">
                                <i class="fas fa-thumbs-up"></i> قبول العرض
                            </button>
                            <button class="btn btn-danger btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#rejectOfferModal">
                                <i class="fas fa-thumbs-down"></i> رفض العرض
                            </button>
                            <button class="btn btn-warning btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#counterOfferModal">
                                <i class="fas fa-exchange-alt"></i> عرض مضاد
                            </button>
                        @endif

                        @if ($order->status_code == 'customer_approved' && !$order->final_approved)
                            <button class="btn btn-success btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#finalApprovalModal">
                                <i class="fas fa-check-double"></i> الموافقة النهائية
                            </button>
                        @endif

                        @if ($order->status_code == 'approved' && !$order->driver_id)
                            <button class="btn btn-info btn-block mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#assignModal">
                                <i class="fas fa-user-check"></i> تعيين سائق
                            </button>
                        @endif

                        @if ($order->status_code == 'approved' && $order->driver_id)
                            <form action="{{ route('orders.negotiation.dispatch', $order) }}" method="POST"
                                class="d-inline w-100">
                                @csrf
                                <button type="submit" class="btn btn-info btn-block mb-2 w-100">
                                    <i class="fas fa-truck"></i> إرسال للتنفيذ
                                </button>
                            </form>
                        @endif

                        @if (!in_array($order->status_code, ['completed', 'cancelled']))
                            <button class="btn btn-outline-danger btn-block w-100" data-bs-toggle="modal"
                                data-bs-target="#cancelModal">
                                <i class="fas fa-times"></i> إلغاء الطلب
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- سجل التفاوض والخط الزمني -->
            <div class="col-lg-8">
                <!-- التبويبات -->
                <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="negotiation-tab" data-bs-toggle="tab" href="#negotiation"
                            role="tab">
                            <i class="fas fa-comments"></i> سجل التفاوض
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="timeline-tab" data-bs-toggle="tab" href="#timeline" role="tab">
                            <i class="fas fa-history"></i> الخط الزمني
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="notes-tab" data-bs-toggle="tab" href="#notes" role="tab">
                            <i class="fas fa-sticky-note"></i> الملاحظات
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="orderTabsContent">
                    <!-- سجل التفاوض -->
                    <div class="tab-pane fade show active" id="negotiation" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                @if ($negotiations->count() > 0)
                                    <div class="negotiation-timeline">
                                        @foreach ($negotiations as $neg)
                                            <div
                                                class="negotiation-item {{ $neg->isFromBranch() ? 'from-branch' : 'from-requester' }}">
                                                <div class="negotiation-header">
                                                    <span
                                                        class="badge bg-{{ $neg->isAccepted() ? 'success' : ($neg->isRejected() ? 'danger' : 'primary') }}">
                                                        {{ $neg->stage_label }}
                                                    </span>
                                                    <small
                                                        class="text-muted">{{ $neg->created_at->format('Y-m-d H:i') }}</small>
                                                </div>
                                                <div class="negotiation-body">
                                                    @if ($neg->offered_price)
                                                        <p><strong>السعر:</strong>
                                                            {{ number_format($neg->offered_price, 2) }} د.ع</p>
                                                    @endif
                                                    @if ($neg->offered_quantity)
                                                        <p><strong>الكمية:</strong>
                                                            {{ number_format($neg->offered_quantity, 2) }} م³</p>
                                                    @endif
                                                    @if ($neg->offered_delivery_date)
                                                        <p><strong>تاريخ التسليم:</strong>
                                                            {{ $neg->offered_delivery_date->format('Y-m-d') }}</p>
                                                    @endif
                                                    @if ($neg->notes)
                                                        <p><strong>ملاحظات:</strong> {{ $neg->notes }}</p>
                                                    @endif
                                                    @if ($neg->rejection_reason)
                                                        <p class="text-danger"><strong>سبب الرفض:</strong>
                                                            {{ $neg->rejection_reason }}</p>
                                                    @endif
                                                </div>
                                                <div class="negotiation-footer">
                                                    <small>بواسطة: {{ $neg->creator->name ?? 'النظام' }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-comments fa-3x mb-3"></i>
                                        <p>لا يوجد سجل تفاوض حتى الآن</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- الخط الزمني -->
                    <div class="tab-pane fade" id="timeline" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                @if ($timeline->count() > 0)
                                    <div class="timeline">
                                        @foreach ($timeline as $event)
                                            <div class="timeline-item">
                                                <div class="timeline-icon">
                                                    <i class="fas {{ $event->event_icon }}"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h6>{{ $event->title }}</h6>
                                                    @if ($event->description)
                                                        <p>{{ $event->description }}</p>
                                                    @endif
                                                    <small class="text-muted">
                                                        {{ $event->created_at->format('Y-m-d H:i') }}
                                                        - {{ $event->created_by_name ?? 'النظام' }}
                                                    </small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-history fa-3x mb-3"></i>
                                        <p>لا يوجد سجل زمني حتى الآن</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="tab-pane fade" id="notes" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form action="{{ route('orders.negotiation.note', $order) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">إضافة ملاحظة جديدة</label>
                                        <textarea name="note" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> إضافة ملاحظة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal مراجعة الفرع -->
    <div class="modal fade" id="branchReviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.branch-review', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">مراجعة الفرع</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">القرار</label>
                            <select name="approved" class="form-select" required>
                                <option value="1">موافقة</option>
                                <option value="0">رفض</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal إرسال عرض سعر -->
    <div class="modal fade" id="sendOfferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.send-offer', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إرسال عرض سعر</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">السعر <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الكمية</label>
                            <input type="number" name="quantity" class="form-control" step="0.01"
                                value="{{ $order->quantity }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ التسليم</label>
                            <input type="date" name="delivery_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">وقت التسليم</label>
                            <input type="time" name="delivery_time" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">إرسال العرض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal قبول العرض -->
    <div class="modal fade" id="acceptOfferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.accept', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد قبول العرض</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من قبول العرض بسعر
                            <strong>{{ number_format($order->branch_price ?? 0, 2) }}</strong> د.ع؟</p>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات (اختياري)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد القبول</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal رفض العرض -->
    <div class="modal fade" id="rejectOfferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.reject', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض العرض</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal عرض مضاد -->
    <div class="modal fade" id="counterOfferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.counter', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تقديم عرض مضاد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">السعر المقترح <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الكمية المطلوبة</label>
                            <input type="number" name="quantity" class="form-control" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">إرسال العرض المضاد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal الموافقة النهائية -->
    <div class="modal fade" id="finalApprovalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.final-approval', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">الموافقة النهائية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">السعر النهائي</label>
                            <input type="number" name="final_price" class="form-control" step="0.01"
                                value="{{ $order->final_price ?? $order->branch_price }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ التنفيذ</label>
                            <input type="date" name="execution_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">وقت التنفيذ</label>
                            <input type="time" name="execution_time" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد الموافقة النهائية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal إلغاء الطلب -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.negotiation.cancel', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إلغاء الطلب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            هذا الإجراء لا يمكن التراجع عنه!
                        </div>
                        <div class="mb-3">
                            <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                        <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .negotiation-timeline {
            position: relative;
        }

        .negotiation-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .negotiation-item.from-branch {
            background-color: #e3f2fd;
            margin-right: 20%;
        }

        .negotiation-item.from-requester {
            background-color: #fff3e0;
            margin-left: 20%;
        }

        .negotiation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .negotiation-body p {
            margin-bottom: 5px;
        }

        .negotiation-footer {
            text-align: left;
            color: #666;
            font-size: 0.85em;
            margin-top: 10px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ddd;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-icon {
            position: absolute;
            right: -30px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .timeline-content {
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .timeline-content h6 {
            margin-bottom: 5px;
        }

        .timeline-content p {
            margin-bottom: 5px;
            color: #666;
        }
    </style>
@endpush
