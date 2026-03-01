<?php

/**
 * Routes لنظام إدارة طلبات الكونكريت
 * 
 * يمكن إضافة هذه الـ Routes إلى ملف routes/web.php
 */

use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // ==================== طلبات العمل ====================

    // عرض القائمة
    Route::get('/work-orders', [WorkOrderController::class, 'index'])
        ->name('work-orders.index');

    // إنشاء طلب جديد
    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])
        ->name('work-orders.create');

    Route::post('/work-orders', [WorkOrderController::class, 'store'])
        ->name('work-orders.store');

    // عرض تفاصيل الطلب
    Route::get('/work-orders/{id}', [WorkOrderController::class, 'show'])
        ->name('work-orders.show');


    // ==================== إدارة الحالات ====================

    // تحويل للمراجعة
    Route::post('/work-orders/{id}/review', [WorkOrderController::class, 'review'])
        ->name('work-orders.review')
        ->middleware('can:review,work_order');

    // الموافقة
    Route::post('/work-orders/{id}/approve', [WorkOrderController::class, 'approve'])
        ->name('work-orders.approve')
        ->middleware('can:approve,work_order');

    // الرفض
    Route::post('/work-orders/{id}/reject', [WorkOrderController::class, 'reject'])
        ->name('work-orders.reject')
        ->middleware('can:reject,work_order');

    // الجدولة
    Route::post('/work-orders/{id}/schedule', [WorkOrderController::class, 'schedule'])
        ->name('work-orders.schedule')
        ->middleware('can:schedule,work_order');

    // الإلغاء
    Route::post('/work-orders/{id}/cancel', [WorkOrderController::class, 'cancel'])
        ->name('work-orders.cancel')
        ->middleware('can:cancel,work_order');


    // ==================== إدارة الأسعار ====================

    Route::post('/work-orders/{id}/change-price', [WorkOrderController::class, 'changePrice'])
        ->name('work-orders.change-price')
        ->middleware('can:changePrice,work_order');


    // ==================== التنفيذ ====================

    // إضافة تنفيذ جزئي
    Route::post('/work-orders/{id}/executions', [WorkOrderController::class, 'addExecution'])
        ->name('work-orders.executions.add')
        ->middleware('can:addExecution,work_order');

    // تحديث حالة التنفيذ
    Route::put('/work-orders/{orderId}/executions/{executionId}', [WorkOrderController::class, 'updateExecutionStatus'])
        ->name('work-orders.executions.update')
        ->middleware('can:updateExecution,work_order');


    // ==================== التقارير والطباعة ====================

    // طباعة الطلب
    Route::get('/work-orders/{id}/print', [WorkOrderController::class, 'print'])
        ->name('work-orders.print');

    // تصدير Excel
    Route::get('/work-orders/export', [WorkOrderController::class, 'export'])
        ->name('work-orders.export')
        ->middleware('can:export,work_orders');


    // ==================== API / AJAX ====================

    // إحصائيات
    Route::get('/api/work-orders/statistics', [WorkOrderController::class, 'statistics'])
        ->name('api.work-orders.statistics');
});


/**
 * مثال على استخدام الـ Routes:
 * 
 * // عرض قائمة الطلبات
 * <a href="{{ route('work-orders.index') }}">الطلبات</a>
 * 
 * // إنشاء طلب جديد
 * <a href="{{ route('work-orders.create') }}">طلب جديد</a>
 * 
 * // عرض طلب
 * <a href="{{ route('work-orders.show', $order->id) }}">عرض</a>
 * 
 * // الموافقة على طلب
 * <form action="{{ route('work-orders.approve', $order->id) }}" method="POST">
 *     @csrf
 *     <button type="submit">موافقة</button>
 * </form>
 * 
 * // إضافة تنفيذ
 * <form action="{{ route('work-orders.executions.add', $order->id) }}" method="POST">
 *     @csrf
 *     <input type="number" name="quantity" required>
 *     <button type="submit">إضافة تنفيذ</button>
 * </form>
 */
