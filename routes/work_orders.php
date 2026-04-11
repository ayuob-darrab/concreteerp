<?php

/**
 * مسارات نظام إدارة طلبات الكونكريت — تُحمَّل من bootstrap/app.php مع routes/web.php
 */

use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // مسارات ثابتة قبل {work_order} لتجنب التقاط "export" أو "create" كمعرّف
    Route::get('/work-orders', [WorkOrderController::class, 'index'])
        ->name('work-orders.index');

    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])
        ->name('work-orders.create');

    Route::post('/work-orders', [WorkOrderController::class, 'store'])
        ->name('work-orders.store');

    Route::get('/work-orders/export', [WorkOrderController::class, 'export'])
        ->name('work-orders.export')
        ->middleware('can:export,App\Models\WorkOrder');

    Route::get('/api/work-orders/statistics', [WorkOrderController::class, 'statistics'])
        ->name('api.work-orders.statistics');

    // تفاصيل الطلب والإجراءات
    Route::get('/work-orders/{work_order}', [WorkOrderController::class, 'show'])
        ->name('work-orders.show');

    Route::post('/work-orders/{work_order}/review', [WorkOrderController::class, 'review'])
        ->name('work-orders.review')
        ->middleware('can:review,work_order');

    Route::post('/work-orders/{work_order}/approve', [WorkOrderController::class, 'approve'])
        ->name('work-orders.approve')
        ->middleware('can:approve,work_order');

    Route::post('/work-orders/{work_order}/reject', [WorkOrderController::class, 'reject'])
        ->name('work-orders.reject')
        ->middleware('can:reject,work_order');

    Route::post('/work-orders/{work_order}/schedule', [WorkOrderController::class, 'schedule'])
        ->name('work-orders.schedule')
        ->middleware('can:schedule,work_order');

    Route::post('/work-orders/{work_order}/cancel', [WorkOrderController::class, 'cancel'])
        ->name('work-orders.cancel')
        ->middleware('can:cancel,work_order');

    Route::post('/work-orders/{work_order}/change-price', [WorkOrderController::class, 'changePrice'])
        ->name('work-orders.change-price')
        ->middleware('can:changePrice,work_order');

    Route::post('/work-orders/{work_order}/executions', [WorkOrderController::class, 'addExecution'])
        ->name('work-orders.executions.add')
        ->middleware('can:addExecution,work_order');

    Route::put('/work-orders/{work_order}/executions/{execution}', [WorkOrderController::class, 'updateExecutionStatus'])
        ->name('work-orders.executions.update')
        ->middleware('can:updateExecution,work_order');

    Route::get('/work-orders/{work_order}/print', [WorkOrderController::class, 'print'])
        ->name('work-orders.print');
});
