<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyInformationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PricingCategoryController;
use App\Http\Controllers\CompanyNotificationController;
use App\Http\Controllers\SupportTicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Contractor\InvoiceController;
use App\Http\Controllers\Contractor\CheckController;
use App\Http\Controllers\Contractor\ReceiptController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaterialsController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\OrderNegotiationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\VehicleReservationController;
use App\Http\Controllers\WorkJobController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DriverAppController;
use App\Http\Controllers\LossController;
use App\Http\Controllers\PaymentCardController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'loginuser'])->middleware('throttle:4,1');

// صفحة تعريفية بفوائد النظام (متاحة قبل وبعد تسجيل الدخول)
Route::view('/system-benefits', 'system-benefits')->name('system-benefits');

Route::middleware('auth')->group(function () {

    // Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('/register', [RegisterController::class, 'register']);


    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/password/change', [\App\Http\Controllers\Auth\PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // Dashboard
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', function () {
        return view('home');
    });


    Route::get('testpage', [Controller::class, 'testpage'])->name('testpage');



    route::resource('accounts', AccountsController::class);

    // مسارات تعطيل/تفعيل المستخدمين بسبب الاشتراك
    Route::post('/accounts/{id}/deactivate-subscription', [AccountsController::class, 'deactivateForSubscription'])
        ->name('accounts.deactivate-subscription');
    Route::post('/accounts/{id}/reactivate-subscription', [AccountsController::class, 'reactivateFromSubscription'])
        ->name('accounts.reactivate-subscription');
    // تحديث حد المستخدمين (مدير الشركة فقط)
    Route::post('/accounts/update-users-limit', [AccountsController::class, 'updateUsersLimit'])->name('accounts.update-users-limit');

    // ============================================
    // Work Jobs & Shipments Routes - أوامر العمل والشحنات
    // (يجب أن تكون قبل resource route لتأخذ الأولوية)
    // ============================================
    Route::prefix('companyBranch')->middleware(['auth'])->group(function () {
        // لوحة تحكم التنفيذ
        Route::get('/execution/dashboard', [CompanyBranchController::class, 'executionDashboard'])->name('companyBranch.execution.dashboard');
        Route::get('/company-orders-dashboard', [CompanyBranchController::class, 'companyOrdersDashboard'])->name('companyBranch.company.orders.dashboard');

        // صفحات أوامر العمل
        Route::get('/workJobs/today', [CompanyBranchController::class, 'workJobsToday'])->name('companyBranch.workJobs.today');
        Route::get('/workJobs/pending', [CompanyBranchController::class, 'workJobsPending'])->name('companyBranch.workJobs.pending');
        Route::get('/workJobs/active', [CompanyBranchController::class, 'workJobsActive'])->name('companyBranch.workJobs.active');
        Route::get('/workJobs/completed', [CompanyBranchController::class, 'workJobsCompleted'])->name('companyBranch.workJobs.completed');
        Route::get('/workShipments', [CompanyBranchController::class, 'workShipments'])->name('companyBranch.workShipments');

        // عرض تفاصيل أمر العمل
        Route::get('/workJob/{id}/view', [CompanyBranchController::class, 'viewWorkJob'])->name('companyBranch.workJob.view');
        // تخصيص الآليات والسائقين
        Route::get('/workJob/{id}/assign', [CompanyBranchController::class, 'assignWorkJob'])->name('companyBranch.workJob.assign');
        Route::post('/workJob/{id}/saveAssignment', [CompanyBranchController::class, 'saveAssignment'])->name('companyBranch.workJob.saveAssignment');
        // تعيين البَم
        Route::get('/workJob/{id}/assignPump', [CompanyBranchController::class, 'assignPump'])->name('companyBranch.workJob.assignPump');
        Route::post('/workJob/{id}/savePump', [CompanyBranchController::class, 'savePump'])->name('companyBranch.workJob.savePump');
        Route::post('/workJob/{id}/removePump', [CompanyBranchController::class, 'removePump'])->name('companyBranch.workJob.removePump');
        // بدء تنفيذ أمر العمل
        Route::post('/workJob/{id}/start', [CompanyBranchController::class, 'startWorkJob'])->name('companyBranch.workJob.start');
        // إكمال أمر العمل
        Route::post('/workJob/{id}/complete', [CompanyBranchController::class, 'completeWorkJob'])->name('companyBranch.workJob.complete');
        // إضافة شحنة لأمر العمل
        Route::post('/workJob/{id}/addShipment', [CompanyBranchController::class, 'addShipment'])->name('companyBranch.workJob.addShipment');
        // فاتورة أمر العمل
        Route::get('/workJob/{id}/invoice', [CompanyBranchController::class, 'workJobInvoice'])->name('companyBranch.workJob.invoice');

        // عمليات الشحنات
        Route::get('/shipment/{id}/view', [CompanyBranchController::class, 'viewShipment'])->name('companyBranch.shipment.view');
        Route::post('/shipment/{id}/depart', [CompanyBranchController::class, 'departShipment'])->name('companyBranch.shipment.depart');
        Route::post('/shipment/{id}/arrive', [CompanyBranchController::class, 'arriveShipment'])->name('companyBranch.shipment.arrive');
        Route::post('/shipment/{id}/startWork', [CompanyBranchController::class, 'startShipmentWork'])->name('companyBranch.shipment.startWork');
        Route::post('/shipment/{id}/complete', [CompanyBranchController::class, 'completeShipment'])->name('companyBranch.shipment.complete');
        Route::post('/shipment/{id}/reportLoss', [CompanyBranchController::class, 'reportShipmentLoss'])->name('companyBranch.shipment.reportLoss');
        Route::post('/shipment/{id}/cancel', [CompanyBranchController::class, 'cancelShipment'])->name('companyBranch.shipment.cancel');
    });

    route::resource('companyBranch', CompanyBranchController::class);

    route::resource('Employees', EmployeeController::class);

    // ============================================
    // Employee Account Routes - حسابات الموظفين
    // ============================================
    Route::get('/employee/{id}/create-account', [EmployeeController::class, 'showCreateAccount'])->name('employee.createAccount');
    Route::post('/employee/{id}/create-account', [EmployeeController::class, 'storeUserAccount'])->name('employee.storeAccount');
    Route::get('/api/available-cars', [EmployeeController::class, 'getAvailableCars'])->name('api.availableCars');

    route::resource('materials', MaterialsController::class);
    route::resource('cars', CarsController::class);

    // ============================================
    // Car Driver Assignment Routes - تعيين السائقين
    // ============================================
    Route::post('/cars/{id}/end-driver-assignment', [CarsController::class, 'endDriverAssignment'])->name('cars.endDriverAssignment');
    Route::get('/api/drivers-by-shift', [CarsController::class, 'getDriversByShift'])->name('api.driversByShift');

    // ============================================
    // Manager Financial Reports - التقارير المالية للمدير
    // ============================================
    Route::prefix('manager-reports')->middleware(['auth', 'company.manager'])->group(function () {
        Route::get('/', [\App\Http\Controllers\ManagerReportController::class, 'index'])->name('manager-reports.index');

        // تقرير الطلبات المالي
        Route::get('/orders', [\App\Http\Controllers\ManagerReportController::class, 'ordersFinancialReport'])->name('manager-reports.orders');
        Route::get('/orders/print', [\App\Http\Controllers\ManagerReportController::class, 'printOrdersReport'])->name('manager-reports.orders.print');

        // تقرير الصيانة المالي
        Route::get('/maintenance', [\App\Http\Controllers\ManagerReportController::class, 'maintenanceFinancialReport'])->name('manager-reports.maintenance');
        Route::get('/maintenance/print', [\App\Http\Controllers\ManagerReportController::class, 'printMaintenanceReport'])->name('manager-reports.maintenance.print');
    });

    // ============================================
    // Car Maintenance Routes - صيانة السيارات
    // ============================================
    Route::prefix('car-maintenance')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\CarMaintenanceController::class, 'index'])->name('car-maintenance.index');
        Route::get('/report', [\App\Http\Controllers\CarMaintenanceController::class, 'report'])->name('car-maintenance.report');
        Route::get('/car/{carId}', [\App\Http\Controllers\CarMaintenanceController::class, 'carDetails'])->name('car-maintenance.car-details');
        Route::get('/car/{carId}/create', [\App\Http\Controllers\CarMaintenanceController::class, 'create'])->name('car-maintenance.create');
        Route::post('/car/{carId}', [\App\Http\Controllers\CarMaintenanceController::class, 'store'])->name('car-maintenance.store');
        Route::post('/car/{carId}/start', [\App\Http\Controllers\CarMaintenanceController::class, 'startMaintenance'])->name('car-maintenance.start');
        Route::get('/{id}/edit', [\App\Http\Controllers\CarMaintenanceController::class, 'edit'])->name('car-maintenance.edit');
        Route::put('/{id}', [\App\Http\Controllers\CarMaintenanceController::class, 'update'])->name('car-maintenance.update');
        Route::post('/{id}/complete', [\App\Http\Controllers\CarMaintenanceController::class, 'completeMaintenance'])->name('car-maintenance.complete');
        Route::delete('/{id}', [\App\Http\Controllers\CarMaintenanceController::class, 'destroy'])->name('car-maintenance.destroy');
    });

    // أنواع السيارات
    Route::get('/car-types', [SuperAdminController::class, 'carTypes'])->name('admin.car-types');
    Route::post('/car-types', [SuperAdminController::class, 'storeCarType'])->name('admin.car-types.store');
    Route::put('/car-types/{id}', [SuperAdminController::class, 'updateCarType'])->name('admin.car-types.update');
    Route::delete('/car-types/{id}', [SuperAdminController::class, 'deleteCarType'])->name('admin.car-types.delete');

    Route::resource('contractors', ContractorController::class);

    // ============================================
    // Contractor Module Routes - نظام المقاولين
    // ============================================
    Route::prefix('contractors')->middleware(['auth'])->group(function () {
        // إجراءات إضافية للمقاولين
        Route::post('/{contractor}/block', [ContractorController::class, 'block'])->name('contractors.block');
        Route::post('/{contractor}/unblock', [ContractorController::class, 'unblock'])->name('contractors.unblock');
        Route::get('/{contractor}/statement', [ContractorController::class, 'statement'])->name('contractors.statement');
        Route::get('/{contractor}/print-statement', [ContractorController::class, 'printStatement'])->name('contractors.print-statement');
        Route::get('/search/quick', [ContractorController::class, 'quickSearch'])->name('contractors.quick-search');
        Route::get('/{contractor}/statistics', [ContractorController::class, 'statistics'])->name('contractors.statistics');
    });

    // ============================================
    // Contractor Invoices - فواتير المقاولين
    // ============================================
    Route::prefix('contractor-invoices')->middleware(['auth'])->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('contractor-invoices.index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('contractor-invoices.create');
        Route::post('/', [InvoiceController::class, 'store'])->name('contractor-invoices.store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('contractor-invoices.show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('contractor-invoices.edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('contractor-invoices.update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('contractor-invoices.destroy');

        // إجراءات خاصة بالفواتير
        Route::post('/{invoice}/issue', [InvoiceController::class, 'issue'])->name('contractor-invoices.issue');
        Route::post('/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('contractor-invoices.cancel');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('contractor-invoices.print');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('contractor-invoices.download');
        Route::post('/from-work-order/{workOrder}', [InvoiceController::class, 'createFromWorkOrder'])->name('contractor-invoices.from-work-order');
        Route::get('/overdue/list', [InvoiceController::class, 'overdueList'])->name('contractor-invoices.overdue');
        Route::get('/statistics/summary', [InvoiceController::class, 'statistics'])->name('contractor-invoices.statistics');
    });

    // ============================================
    // Contractor Checks - شيكات المقاولين
    // ============================================
    Route::prefix('contractor-checks')->middleware(['auth'])->group(function () {
        Route::get('/', [CheckController::class, 'index'])->name('contractor-checks.index');
        Route::get('/create', [CheckController::class, 'create'])->name('contractor-checks.create');
        Route::post('/', [CheckController::class, 'store'])->name('contractor-checks.store');
        Route::get('/{check}', [CheckController::class, 'show'])->name('contractor-checks.show');
        Route::get('/{check}/edit', [CheckController::class, 'edit'])->name('contractor-checks.edit');
        Route::put('/{check}', [CheckController::class, 'update'])->name('contractor-checks.update');
        Route::delete('/{check}', [CheckController::class, 'destroy'])->name('contractor-checks.destroy');

        // إجراءات خاصة بالشيكات
        Route::post('/{check}/deposit', [CheckController::class, 'deposit'])->name('contractor-checks.deposit');
        Route::post('/{check}/collect', [CheckController::class, 'collect'])->name('contractor-checks.collect');
        Route::post('/{check}/reject', [CheckController::class, 'reject'])->name('contractor-checks.reject');
        Route::post('/{check}/return', [CheckController::class, 'return'])->name('contractor-checks.return');
        Route::post('/{check}/cancel', [CheckController::class, 'cancel'])->name('contractor-checks.cancel');
        Route::post('/{check}/endorse', [CheckController::class, 'endorse'])->name('contractor-checks.endorse');

        // قوائم الشيكات المستحقة
        Route::get('/due/today', [CheckController::class, 'dueToday'])->name('contractor-checks.due-today');
        Route::get('/due/week', [CheckController::class, 'dueThisWeek'])->name('contractor-checks.due-week');
        Route::get('/overdue/list', [CheckController::class, 'overdueList'])->name('contractor-checks.overdue');
        Route::get('/dashboard/summary', [CheckController::class, 'dashboard'])->name('contractor-checks.dashboard');
    });

    // ============================================
    // Contractor Receipts - سندات المقاولين
    // ============================================
    Route::prefix('contractor-receipts')->middleware(['auth'])->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('contractor-receipts.index');
        Route::get('/create-receipt', [ReceiptController::class, 'createReceiptForm'])->name('contractor-receipts.create-receipt');
        Route::post('/receipt', [ReceiptController::class, 'createReceipt'])->name('contractor-receipts.store-receipt');
        Route::get('/create-payment', [ReceiptController::class, 'createPaymentForm'])->name('contractor-receipts.create-payment');
        Route::post('/payment', [ReceiptController::class, 'createPayment'])->name('contractor-receipts.store-payment');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('contractor-receipts.show');
        Route::post('/{receipt}/approve', [ReceiptController::class, 'approve'])->name('contractor-receipts.approve');
        Route::post('/{receipt}/cancel', [ReceiptController::class, 'cancel'])->name('contractor-receipts.cancel');
        Route::get('/{receipt}/print', [ReceiptController::class, 'print'])->name('contractor-receipts.print');
        Route::post('/settle-invoice', [ReceiptController::class, 'settleInvoice'])->name('contractor-receipts.settle-invoice');
    });

    // Companies management - Super Admin only
    Route::middleware(['super.admin'])->group(function () {
        Route::resource('companies', CompanyController::class);
        Route::get('companies/{id}/print-creation-invoice', [CompanyController::class, 'printCreationInvoice'])
            ->name('companies.print-creation-invoice');
    });


    Route::resource('warehouse', WarehouseController::class);

    // مسارات تسديد الموردين
    Route::get('/suppliers/{id}/details', [WarehouseController::class, 'supplierDetails'])->name('suppliers.details');
    Route::post('/suppliers/{id}/payment', [WarehouseController::class, 'storePayment'])->name('suppliers.payment.store');
    Route::get('/suppliers/payment/{id}/print', [WarehouseController::class, 'printPayment'])->name('suppliers.payment.print');

    // ============================================
    // Subscriptions (Super Admin only) - إدارة الاشتراكات
    // ============================================
    Route::middleware('super.admin')->group(function () {
        Route::get('/subscriptions/plans', [\App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscriptions.plans');
        Route::get('/subscriptions/companies', [\App\Http\Controllers\SubscriptionController::class, 'companies'])->name('subscriptions.companies');
        Route::get('/subscriptions/companies/{code}/edit', [\App\Http\Controllers\SubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::post('/subscriptions/companies/{code}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
        Route::post('/subscriptions/companies/{code}/toggle-suspension', [\App\Http\Controllers\SubscriptionController::class, 'toggleSuspension'])->name('subscriptions.toggleSuspension');
        Route::post('/subscriptions/companies/{code}/terminate', [\App\Http\Controllers\SubscriptionController::class, 'terminateSubscription'])->name('subscriptions.terminate');
        Route::post('/subscriptions/companies/{code}/extend', [\App\Http\Controllers\SubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('/subscriptions/companies/{code}/payment', [\App\Http\Controllers\SubscriptionController::class, 'payment'])->name('subscriptions.payment');
        Route::post('/subscriptions/companies/{code}/deduct-extension', [\App\Http\Controllers\SubscriptionController::class, 'deductExtension'])->name('subscriptions.deductExtension');
        Route::get('/subscriptions/companies/{code}/extensions', [\App\Http\Controllers\SubscriptionController::class, 'extensionHistory'])->name('subscriptions.extensions');
        Route::get('/subscriptions/monitor', [\App\Http\Controllers\SubscriptionController::class, 'monitor'])->name('subscriptions.monitor');
        Route::get('/subscriptions/financial-reports', [\App\Http\Controllers\SubscriptionController::class, 'financialReports'])->name('subscriptions.financial');
        Route::get('/subscriptions/companies/{code}/history', [\App\Http\Controllers\SubscriptionController::class, 'subscriptionHistory'])->name('subscriptions.history');
        Route::get('/subscriptions/invoice/{id}', [\App\Http\Controllers\SubscriptionController::class, 'invoice'])->name('subscriptions.invoice');
        Route::get('/subscriptions/payment-invoice/{id}', [\App\Http\Controllers\SubscriptionController::class, 'paymentInvoice'])->name('subscriptions.payment-invoice');

        // Subscription Pricing Settings - إعدادات أسعار الاشتراكات
        Route::get('/subscriptions/settings', [\App\Http\Controllers\SubscriptionController::class, 'pricingSettings'])->name('subscriptions.settings');
        Route::post('/subscriptions/settings', [\App\Http\Controllers\SubscriptionController::class, 'updatePricingSettings'])->name('subscriptions.settings.update');
        Route::post('/subscriptions/company-pricing/{companyCode}', [\App\Http\Controllers\SubscriptionController::class, 'updateCompanyPricing'])->name('subscriptions.company-pricing.update');
        Route::delete('/subscriptions/company-pricing/{companyCode}', [\App\Http\Controllers\SubscriptionController::class, 'deleteCompanyPricing'])->name('subscriptions.company-pricing.delete');

        // زيادة عدد المستخدمين
        Route::post('/subscriptions/add-users', [\App\Http\Controllers\SubscriptionController::class, 'addUsers'])->name('subscriptions.add-users');
    });

    // Payment Cards Routes - مسارات حسابات الدفع الإلكتروني
    Route::resource('payment-cards', PaymentCardController::class);
    Route::post('/payment-cards/{id}/toggle-status', [\App\Http\Controllers\PaymentCardController::class, 'toggleStatus'])->name('payment-cards.toggle-status');
    Route::post('/payment-cards/{id}/deposit', [\App\Http\Controllers\PaymentCardController::class, 'deposit'])->name('payment-cards.deposit');
    Route::post('/payment-cards/{id}/withdraw', [\App\Http\Controllers\PaymentCardController::class, 'withdraw'])->name('payment-cards.withdraw');
    Route::get('/payment-cards/api/active', [\App\Http\Controllers\PaymentCardController::class, 'getActiveCards'])->name('payment-cards.api.active');
    Route::get('/payment-cards/api/{id}', [\App\Http\Controllers\PaymentCardController::class, 'getCardDetails'])->name('payment-cards.api.details');
    Route::get('/payment-cards-report/transactions', [\App\Http\Controllers\PaymentCardController::class, 'transactionsReport'])->name('payment-cards.transactions');

    // ============================================
    // Company Payment Cards - بطاقات الدفع الخاصة بالشركات
    // ============================================
    Route::resource('company-payment-cards', \App\Http\Controllers\CompanyPaymentCardController::class);
    Route::post('/company-payment-cards/{id}/toggle-status', [\App\Http\Controllers\CompanyPaymentCardController::class, 'toggleStatus'])->name('company-payment-cards.toggle-status');
    Route::post('/company-payment-cards/{id}/deposit', [\App\Http\Controllers\CompanyPaymentCardController::class, 'deposit'])->name('company-payment-cards.deposit');
    Route::post('/company-payment-cards/{id}/withdraw', [\App\Http\Controllers\CompanyPaymentCardController::class, 'withdraw'])->name('company-payment-cards.withdraw');
    Route::get('/company-payment-cards-api/active', [\App\Http\Controllers\CompanyPaymentCardController::class, 'getActiveCards'])->name('company-payment-cards.api.active');
    Route::get('/company-payment-cards-report/transactions', [\App\Http\Controllers\CompanyPaymentCardController::class, 'transactionsReport'])->name('company-payment-cards.transactions');

    // ============================================
    // Branch Payments - مدفوعات الزبائن (الفرع)
    // ============================================
    Route::get('/branch/payments', [\App\Http\Controllers\BranchPaymentController::class, 'index'])->name('branch.payments.index');
    Route::get('/branch/payments/customer/{phone}', [\App\Http\Controllers\BranchPaymentController::class, 'customerPayment'])->name('branch.payments.customer');
    Route::post('/branch/payments/store', [\App\Http\Controllers\BranchPaymentController::class, 'storePayment'])->name('branch.payments.store');
    Route::get('/branch/payments/report', [\App\Http\Controllers\BranchPaymentController::class, 'paymentsReport'])->name('branch.payments.report');
    Route::get('/branch/payments/invoice/{id}', [\App\Http\Controllers\BranchPaymentController::class, 'printInvoice'])->name('branch.payments.invoice');
    Route::get('/branch/payments/branches-report', [\App\Http\Controllers\BranchPaymentController::class, 'branchesReport'])->name('branch.payments.branches-report');
    Route::get('/branch/payments/order/{id}', [\App\Http\Controllers\BranchPaymentController::class, 'getOrderDetails'])->name('branch.payments.order-details');

    // ============================================
    // Super Admin Routes - صلاحيات السوبر أدمن
    // ============================================
    Route::prefix('admin')->middleware(['auth', 'super.admin'])->group(function () {
        // إدارة المستخدمين
        Route::get('/users', [SuperAdminController::class, 'users'])->name('admin.users');
        Route::get('/super-admin-users', [SuperAdminController::class, 'superAdminUsers'])->name('admin.super-admin-users');
        Route::get('/super-admin-users/create', [SuperAdminController::class, 'createSuperAdminUser'])->name('admin.super-admin-users.create');
        Route::post('/super-admin-users', [SuperAdminController::class, 'storeSuperAdminUser'])->name('admin.super-admin-users.store');
        Route::get('/super-admin-users/{id}/edit', [SuperAdminController::class, 'editSuperAdminUser'])->name('admin.super-admin-users.edit');
        Route::put('/super-admin-users/{id}', [SuperAdminController::class, 'updateSuperAdminUser'])->name('admin.super-admin-users.update');
        Route::get('/roles', [SuperAdminController::class, 'roles'])->name('admin.roles');
        Route::post('/roles', [SuperAdminController::class, 'storeRole'])->name('admin.roles.store');
        Route::delete('/roles/{id}', [SuperAdminController::class, 'deleteRole'])->name('admin.roles.delete');
        Route::get('/activity-logs', [SuperAdminController::class, 'activityLogs'])->name('admin.activity-logs');

        // التقارير والإحصائيات
        Route::get('/statistics', [SuperAdminController::class, 'statistics'])->name('admin.statistics');
        Route::get('/performance', [SuperAdminController::class, 'performance'])->name('admin.performance');
        Route::post('/performance/enable-cache', [SuperAdminController::class, 'enableCache'])->name('admin.cache.enable');
        Route::post('/performance/disable-cache', [SuperAdminController::class, 'disableCache'])->name('admin.cache.disable');

        // إعدادات النظام
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::get('/backups', [SuperAdminController::class, 'backups'])->name('admin.backups');
        Route::post('/backups/create', [SuperAdminController::class, 'createBackup'])->name('admin.backups.create');
        Route::post('/backups/auto-settings', [SuperAdminController::class, 'updateAutoBackupSettings'])->name('admin.backups.auto-settings');
        Route::get('/backups/download/{filename}', [SuperAdminController::class, 'downloadBackup'])->name('admin.backups.download');
        Route::delete('/backups/{filename}', [SuperAdminController::class, 'deleteBackup'])->name('admin.backups.delete');
        Route::get('/notifications', [SuperAdminController::class, 'notifications'])->name('admin.notifications');
        Route::post('/notifications/send', [SuperAdminController::class, 'sendNotification'])->name('admin.notifications.send');
        Route::get('/notifications/list', [SuperAdminController::class, 'notificationsList'])->name('admin.notifications.list');
        Route::get('/notifications/{id}/details', [SuperAdminController::class, 'notificationDetails'])->name('admin.notifications.details');

        // البيانات الأساسية
        Route::get('/cities', [SuperAdminController::class, 'cities'])->name('admin.cities');
        Route::post('/cities', [SuperAdminController::class, 'storeCity'])->name('admin.cities.store');
        Route::put('/cities/{id}', [SuperAdminController::class, 'updateCity'])->name('admin.cities.update');
        Route::delete('/cities/{id}', [SuperAdminController::class, 'deleteCity'])->name('admin.cities.delete');

        Route::get('/employee-types', [SuperAdminController::class, 'employeeTypes'])->name('admin.employee-types');
        Route::post('/employee-types', [SuperAdminController::class, 'storeEmployeeType'])->name('admin.employee-types.store');
        Route::put('/employee-types/{id}', [SuperAdminController::class, 'updateEmployeeType'])->name('admin.employee-types.update');
        Route::delete('/employee-types/{id}', [SuperAdminController::class, 'deleteEmployeeType'])->name('admin.employee-types.delete');

        // تم نقل أنواع السيارات إلى صلاحيات مدير الشركة

        // الدعم والصيانة
        Route::get('/tickets', [SuperAdminController::class, 'tickets'])->name('admin.tickets');
        Route::get('/tickets/{id}', [SuperAdminController::class, 'showTicket'])->name('admin.tickets.show');
        Route::post('/tickets/{id}/reply', [SuperAdminController::class, 'replyTicket'])->name('admin.tickets.reply');
        Route::put('/tickets/{id}/status', [SuperAdminController::class, 'updateTicketStatus'])->name('admin.tickets.status');

        Route::get('/error-logs', [SuperAdminController::class, 'errorLogs'])->name('admin.error-logs');
        Route::delete('/error-logs/clear', [SuperAdminController::class, 'clearErrorLogs'])->name('admin.error-logs.clear');

        Route::get('/system-health', [SuperAdminController::class, 'systemHealth'])->name('admin.system-health');
    });

    // ============================================
    // Pricing Categories Routes - الفئات السعرية
    // ============================================

    // روابط السوبر أدمن للفئات السعرية
    Route::prefix('pricing-categories')->middleware(['auth'])->group(function () {
        Route::get('/', [PricingCategoryController::class, 'index'])->name('pricing-categories.index');
        Route::post('/', [PricingCategoryController::class, 'store'])->name('pricing-categories.store');
        Route::put('/{id}', [PricingCategoryController::class, 'update'])->name('pricing-categories.update');
        Route::delete('/{id}', [PricingCategoryController::class, 'destroy'])->name('pricing-categories.destroy');
        Route::patch('/{id}/toggle', [PricingCategoryController::class, 'toggleStatus'])->name('pricing-categories.toggle');
    });

    // روابط مدير الشركة لأسعار الخلطات
    Route::prefix('company-prices')->middleware(['auth'])->group(function () {
        Route::get('/', [PricingCategoryController::class, 'companyPrices'])->name('pricing-categories.company-prices');
        Route::post('/', [PricingCategoryController::class, 'saveCompanyPrices'])->name('pricing-categories.company-prices.save');
        Route::post('/single', [PricingCategoryController::class, 'saveSinglePrice'])->name('pricing-categories.single-price');
        Route::get('/mix/{mixId}', [PricingCategoryController::class, 'getMixPrices'])->name('pricing-categories.mix-prices');
        Route::get('/cost-details/{mixId}', [PricingCategoryController::class, 'getCostDetails'])->name('pricing-categories.cost-details');
    });

    // ============================================
    // Financial System Routes - النظام المالي
    // ============================================
    Route::prefix('financial')->middleware(['auth', 'company.manager'])->group(function () {
        // الحسابات
        Route::get('/accounts', [FinancialController::class, 'accounts'])->name('financial.accounts');
        Route::post('/accounts', [FinancialController::class, 'createAccount'])->name('financial.accounts.store');
        Route::get('/accounts/{id}', [FinancialController::class, 'showAccount'])->name('financial.accounts.show');
        Route::get('/accounts/{id}/statement', [FinancialController::class, 'accountStatement'])->name('financial.accounts.statement');

        // المعاملات المالية
        Route::get('/transactions', [FinancialController::class, 'transactions'])->name('financial.transactions');
        Route::get('/transactions/pending', [FinancialController::class, 'pendingTransactions'])->name('financial.transactions.pending');
        Route::post('/transactions/{id}/approve', [FinancialController::class, 'approveTransaction'])->name('financial.transactions.approve');
        Route::post('/transactions/{id}/reject', [FinancialController::class, 'rejectTransaction'])->name('financial.transactions.reject');

        // المدفوعات
        Route::get('/payments', [FinancialController::class, 'payments'])->name('financial.payments');
        Route::post('/payments/receive', [FinancialController::class, 'receivePayment'])->name('financial.payments.receive');
        Route::post('/payments/make', [FinancialController::class, 'makePayment'])->name('financial.payments.make');

        // الصندوق
        Route::get('/cash-register', [FinancialController::class, 'cashRegister'])->name('financial.cash-register');
        Route::post('/cash-register', [FinancialController::class, 'addCashEntry'])->name('financial.cash-register.store');

        // التقارير
        Route::get('/reports/daily', [FinancialController::class, 'dailyReport'])->name('financial.reports.daily');
    });

    // ============================================
    // Company Notifications - إشعارات الشركة
    // ============================================
    Route::prefix('company/notifications')->middleware(['auth'])->group(function () {
        Route::get('/', [CompanyNotificationController::class, 'index'])->name('company.notifications');
        Route::get('/count', [CompanyNotificationController::class, 'getNewCount'])->name('company.notifications.count');
        Route::get('/recent', [CompanyNotificationController::class, 'getRecent'])->name('company.notifications.recent');
        Route::get('/{id}', [CompanyNotificationController::class, 'show'])->name('company.notifications.show');
        Route::post('/{id}/mark-read', [CompanyNotificationController::class, 'markAsRead'])->name('company.notifications.mark-read');
        Route::post('/mark-all-read', [CompanyNotificationController::class, 'markAllAsRead'])->name('company.notifications.mark-all-read');
    });

    // ============================================
    // Support Tickets - تذاكر الدعم الفني
    // ============================================
    Route::prefix('support')->middleware(['auth'])->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('support.index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('support.create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('support.store');
        Route::get('/{id}', [SupportTicketController::class, 'show'])->name('support.show');
        Route::post('/{id}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
        Route::post('/{id}/close', [SupportTicketController::class, 'close'])->name('support.close');
        Route::post('/{id}/reopen', [SupportTicketController::class, 'reopen'])->name('support.reopen');
    });

    // ============================================
    // Advances System - نظام السلف والقروض
    // ============================================
    Route::prefix('advances')->middleware(['auth'])->group(function () {
        // القائمة الرئيسية
        Route::get('/', [AdvanceController::class, 'index'])->name('advances.index');
        Route::get('/pending', [AdvanceController::class, 'pending'])->name('advances.pending');
        Route::get('/approved', [AdvanceController::class, 'approved'])->name('advances.approved');
        Route::get('/create', [AdvanceController::class, 'create'])->name('advances.create');
        Route::post('/', [AdvanceController::class, 'store'])->name('advances.store');
        Route::get('/{advance}', [AdvanceController::class, 'show'])->name('advances.show');
        Route::get('/{advance}/edit', [AdvanceController::class, 'edit'])->name('advances.edit');
        Route::put('/{advance}', [AdvanceController::class, 'update'])->name('advances.update');
        Route::delete('/{advance}', [AdvanceController::class, 'destroy'])->name('advances.destroy');

        // إجراءات السلفة
        Route::post('/{advance}/approve', [AdvanceController::class, 'approve'])->name('advances.approve');
        Route::post('/{advance}/approve-with-edit', [AdvanceController::class, 'approveWithEdit'])->name('advances.approve-with-edit');
        Route::post('/{advance}/reject', [AdvanceController::class, 'reject'])->name('advances.reject');
        Route::post('/{advance}/cancel', [AdvanceController::class, 'cancel'])->name('advances.cancel');
        Route::post('/{advance}/toggle-auto-deduction', [AdvanceController::class, 'toggleAutoDeduction'])->name('advances.toggle-auto');

        // الدفعات
        Route::get('/{advance}/payment', [AdvanceController::class, 'showPaymentForm'])->name('advances.payment-form');
        Route::post('/{advance}/payment', [AdvanceController::class, 'makePayment'])->name('advances.payment');

        // الطباعة والتقارير
        Route::get('/{advance}/print', [AdvanceController::class, 'print'])->name('advances.print');
        Route::get('/payment/{payment}/print', [AdvanceController::class, 'printPayment'])->name('advances.payment.print');
        Route::get('/print/statement', [AdvanceController::class, 'printStatement'])->name('advances.statement');
        Route::get('/reports/summary', [AdvanceController::class, 'report'])->name('advances.report');

        // الإعدادات
        Route::get('/settings/manage', [AdvanceController::class, 'settings'])->name('advances.settings');
        Route::post('/settings/manage', [AdvanceController::class, 'saveSettings'])->name('advances.settings.save');
    });

    // ============================================
    // Payroll System - نظام الرواتب
    // ============================================
    Route::prefix('payroll')->middleware(['auth'])->group(function () {
        // كشوفات الرواتب
        Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('/{payroll}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
        Route::post('/{payroll}/cancel', [PayrollController::class, 'cancel'])->name('payroll.cancel');
        Route::get('/{payroll}/print', [PayrollController::class, 'print'])->name('payroll.print');

        // كشوفات جماعية
        Route::post('/generate-bulk', [PayrollController::class, 'generateBulk'])->name('payroll.generate-bulk');
        Route::get('/reports/summary', [PayrollController::class, 'report'])->name('payroll.report');

        // البدلات
        Route::get('/employee/{employee}/allowances', [PayrollController::class, 'allowances'])->name('payroll.allowances');
        Route::post('/employee/{employee}/allowances', [PayrollController::class, 'storeAllowance'])->name('payroll.allowances.store');

        // المكافآت
        Route::get('/employee/{employee}/bonuses', [PayrollController::class, 'bonuses'])->name('payroll.bonuses');
        Route::post('/employee/{employee}/bonuses', [PayrollController::class, 'storeBonus'])->name('payroll.bonuses.store');

        // الخصومات
        Route::get('/employee/{employee}/deductions', [PayrollController::class, 'deductions'])->name('payroll.deductions');
        Route::post('/employee/{employee}/deductions', [PayrollController::class, 'storeDeduction'])->name('payroll.deductions.store');

        // الإجازات
        Route::get('/leaves', [PayrollController::class, 'leaves'])->name('payroll.leaves');
        Route::post('/leaves', [PayrollController::class, 'storeLeave'])->name('payroll.leaves.store');
        Route::post('/leaves/{leave}/approve', [PayrollController::class, 'approveLeave'])->name('payroll.leaves.approve');
        Route::post('/leaves/{leave}/reject', [PayrollController::class, 'rejectLeave'])->name('payroll.leaves.reject');

        // تعديل الراتب
        Route::post('/employee/{employee}/adjust-salary', [PayrollController::class, 'adjustSalary'])->name('payroll.adjust-salary');
    });

    // ============================================
    // Order Negotiation System - نظام التفاوض على الطلبات
    // ============================================
    Route::prefix('orders/negotiation')->middleware(['auth'])->group(function () {
        // قوائم الطلبات
        Route::get('/pending-review', [OrderNegotiationController::class, 'pendingReview'])->name('orders.negotiation.pending-review');
        Route::get('/pending-response', [OrderNegotiationController::class, 'pendingResponse'])->name('orders.negotiation.pending-response');
        Route::get('/in-negotiation', [OrderNegotiationController::class, 'inNegotiation'])->name('orders.negotiation.in-negotiation');
        Route::get('/ready-approval', [OrderNegotiationController::class, 'readyForApproval'])->name('orders.negotiation.ready-approval');
        Route::get('/statistics', [OrderNegotiationController::class, 'statistics'])->name('orders.negotiation.statistics');

        // تفاصيل التفاوض للطلب
        Route::get('/{order}', [OrderNegotiationController::class, 'show'])->name('orders.negotiation.show');
        Route::get('/{order}/timeline', [OrderNegotiationController::class, 'timeline'])->name('orders.negotiation.timeline');

        // إجراءات التفاوض
        Route::post('/{order}/branch-review', [OrderNegotiationController::class, 'branchReview'])->name('orders.negotiation.branch-review');
        Route::post('/{order}/send-offer', [OrderNegotiationController::class, 'sendOffer'])->name('orders.negotiation.send-offer');
        Route::post('/{order}/accept', [OrderNegotiationController::class, 'acceptOffer'])->name('orders.negotiation.accept');
        Route::post('/{order}/reject', [OrderNegotiationController::class, 'rejectOffer'])->name('orders.negotiation.reject');
        Route::post('/{order}/counter', [OrderNegotiationController::class, 'counterOffer'])->name('orders.negotiation.counter');
        Route::post('/{order}/final-approval', [OrderNegotiationController::class, 'finalApproval'])->name('orders.negotiation.final-approval');
        Route::post('/{order}/cancel', [OrderNegotiationController::class, 'cancel'])->name('orders.negotiation.cancel');

        // التعيين والتنفيذ
        Route::post('/{order}/assign', [OrderNegotiationController::class, 'assign'])->name('orders.negotiation.assign');
        Route::post('/{order}/dispatch', [OrderNegotiationController::class, 'sendToExecution'])->name('orders.negotiation.dispatch');
        Route::post('/{order}/complete', [OrderNegotiationController::class, 'complete'])->name('orders.negotiation.complete');

        // الملاحظات
        Route::post('/{order}/note', [OrderNegotiationController::class, 'addNote'])->name('orders.negotiation.note');
    });

    // ============================================
    // Maintenance System - نظام الصيانة
    // ============================================
    Route::prefix('maintenance')->middleware(['auth'])->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/schedule', [MaintenanceController::class, 'schedule'])->name('maintenance.schedule');
        Route::get('/statistics', [MaintenanceController::class, 'statistics'])->name('maintenance.statistics');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
        Route::post('/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::post('/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenance.cancel');
        Route::get('/{maintenance}/print', [MaintenanceController::class, 'print'])->name('maintenance.print');
        Route::get('/vehicle/{vehicle}/report', [MaintenanceController::class, 'report'])->name('maintenance.report');
    });

    // ============================================
    // Vehicle Reservations - حجوزات الآليات
    // ============================================
    Route::prefix('vehicle-reservations')->middleware(['auth'])->group(function () {
        Route::get('/', [VehicleReservationController::class, 'index'])->name('vehicle-reservations.index');
        Route::get('/create', [VehicleReservationController::class, 'create'])->name('vehicle-reservations.create');
        Route::post('/', [VehicleReservationController::class, 'store'])->name('vehicle-reservations.store');
        Route::get('/calendar', [VehicleReservationController::class, 'calendar'])->name('vehicle-reservations.calendar');
        Route::get('/statistics', [VehicleReservationController::class, 'statistics'])->name('vehicle-reservations.statistics');
        Route::get('/find-available', [VehicleReservationController::class, 'findAvailable'])->name('vehicle-reservations.find-available');
        Route::get('/{vehicleReservation}', [VehicleReservationController::class, 'show'])->name('vehicle-reservations.show');
        Route::post('/{vehicleReservation}/confirm', [VehicleReservationController::class, 'confirm'])->name('vehicle-reservations.confirm');
        Route::post('/{vehicleReservation}/start', [VehicleReservationController::class, 'start'])->name('vehicle-reservations.start');
        Route::post('/{vehicleReservation}/complete', [VehicleReservationController::class, 'complete'])->name('vehicle-reservations.complete');
        Route::post('/{vehicleReservation}/cancel', [VehicleReservationController::class, 'cancel'])->name('vehicle-reservations.cancel');
        Route::get('/vehicle/{vehicle}/reservations', [VehicleReservationController::class, 'vehicleReservations'])->name('vehicle-reservations.vehicle');
    });

    // ============================================
    // Work Jobs System - نظام أوامر العمل والتنفيذ
    // ============================================
    Route::prefix('work-jobs')->middleware(['auth'])->group(function () {
        // أوامر العمل الأساسية
        Route::get('/', [WorkJobController::class, 'index'])->name('work-jobs.index');
        Route::get('/daily', [WorkJobController::class, 'daily'])->name('work-jobs.daily');
        Route::get('/create', [WorkJobController::class, 'create'])->name('work-jobs.create');
        Route::post('/', [WorkJobController::class, 'store'])->name('work-jobs.store');
        Route::get('/statistics', [WorkJobController::class, 'statistics'])->name('work-jobs.statistics');
        Route::get('/{workJob}', [WorkJobController::class, 'show'])->name('work-jobs.show');
        Route::get('/{workJob}/edit', [WorkJobController::class, 'edit'])->name('work-jobs.edit');
        Route::put('/{workJob}', [WorkJobController::class, 'update'])->name('work-jobs.update');
        Route::delete('/{workJob}', [WorkJobController::class, 'destroy'])->name('work-jobs.destroy');

        // إجراءات أمر العمل
        Route::post('/{workJob}/reserve-materials', [WorkJobController::class, 'reserveMaterials'])->name('work-jobs.reserve-materials');
        Route::post('/{workJob}/complete', [WorkJobController::class, 'complete'])->name('work-jobs.complete');
        Route::post('/{workJob}/cancel', [WorkJobController::class, 'cancel'])->name('work-jobs.cancel');
        Route::post('/{workJob}/hold', [WorkJobController::class, 'hold'])->name('work-jobs.hold');
        Route::post('/{workJob}/resume', [WorkJobController::class, 'resume'])->name('work-jobs.resume');
        Route::get('/{workJob}/timeline', [WorkJobController::class, 'timeline'])->name('work-jobs.timeline');

        // الطباعة
        Route::get('/{workJob}/print', [WorkJobController::class, 'print'])->name('work-jobs.print');
    });

    // ============================================
    // Shipments - الشحنات والرحلات
    // ============================================
    Route::prefix('shipments')->middleware(['auth'])->group(function () {
        Route::get('/', [ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/active', [ShipmentController::class, 'active'])->name('shipments.active');
        Route::get('/job/{workJob}/create', [ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/job/{workJob}', [ShipmentController::class, 'store'])->name('shipments.store');
        Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::get('/{shipment}/tracking', [ShipmentController::class, 'tracking'])->name('shipments.tracking');
        Route::get('/{shipment}/print', [ShipmentController::class, 'print'])->name('shipments.print');

        // تحديثات حالة الشحنة
        Route::post('/{shipment}/depart', [ShipmentController::class, 'depart'])->name('shipments.depart');
        Route::post('/{shipment}/arrive', [ShipmentController::class, 'arrive'])->name('shipments.arrive');
        Route::post('/{shipment}/start-work', [ShipmentController::class, 'startWork'])->name('shipments.start-work');
        Route::post('/{shipment}/end-work', [ShipmentController::class, 'endWork'])->name('shipments.end-work');
        Route::post('/{shipment}/return', [ShipmentController::class, 'return'])->name('shipments.return');
        Route::post('/{shipment}/cancel', [ShipmentController::class, 'cancel'])->name('shipments.cancel');

        // API الموقع
        Route::get('/{shipment}/location', [ShipmentController::class, 'getLocation'])->name('shipments.location');
        Route::get('/all/locations', [ShipmentController::class, 'getAllLocations'])->name('shipments.all-locations');
    });

    // ============================================
    // Driver App - تطبيق السائق
    // ============================================
    Route::prefix('driver')->middleware(['auth'])->group(function () {
        Route::get('/', [DriverAppController::class, 'dashboard'])->name('driver.dashboard');
        Route::get('/current', [DriverAppController::class, 'currentShipment'])->name('driver.current');
        Route::post('/shipment/{shipment}/status', [DriverAppController::class, 'updateStatus'])->name('driver.update-status');
        Route::post('/shipment/{shipment}/issue', [DriverAppController::class, 'reportIssue'])->name('driver.report-issue');
        Route::get('/history', [DriverAppController::class, 'history'])->name('driver.history');

        // API للموقع
        Route::post('/location', [DriverAppController::class, 'updateLocation'])->name('driver.location');

        // شحناتي - عرض الشحنات المخصصة للسائق
        Route::get('/shipments', [\App\Http\Controllers\DriverShipmentsController::class, 'index'])->name('driver.shipments.index');
        Route::get('/shipments/{shipment}', [\App\Http\Controllers\DriverShipmentsController::class, 'show'])->name('driver.shipments.show');
        Route::post('/shipments/{shipment}/status', [\App\Http\Controllers\DriverShipmentsController::class, 'updateStatus'])->name('driver.shipments.updateStatus');
    });

    // ============================================
    // Losses - إدارة الخسائر
    // ============================================
    Route::prefix('losses')->middleware(['auth'])->group(function () {
        Route::get('/', [LossController::class, 'index'])->name('losses.index');
        Route::get('/create', [LossController::class, 'create'])->name('losses.create');
        Route::post('/', [LossController::class, 'store'])->name('losses.store');
        Route::get('/statistics', [LossController::class, 'statistics'])->name('losses.statistics');
        Route::get('/{loss}', [LossController::class, 'show'])->name('losses.show');
        Route::get('/{loss}/edit', [LossController::class, 'edit'])->name('losses.edit');
        Route::put('/{loss}', [LossController::class, 'update'])->name('losses.update');
        Route::delete('/{loss}', [LossController::class, 'destroy'])->name('losses.destroy');

        // إجراءات التحقيق
        Route::post('/{loss}/investigate', [LossController::class, 'investigate'])->name('losses.investigate');
        Route::post('/{loss}/resolve', [LossController::class, 'resolve'])->name('losses.resolve');
        Route::post('/{loss}/close', [LossController::class, 'close'])->name('losses.close');
    });

    // ============================================
    // Notifications - نظام الإشعارات
    // ============================================
    Route::prefix('notifications')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/dropdown', [\App\Http\Controllers\NotificationController::class, 'dropdown'])->name('notifications.dropdown');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/read/all', [\App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('notifications.destroy-read');

        // إعدادات الإشعارات
        Route::get('/settings', [\App\Http\Controllers\NotificationSettingController::class, 'index'])->name('notifications.settings');
        Route::put('/settings', [\App\Http\Controllers\NotificationSettingController::class, 'update'])->name('notifications.settings.update');
        Route::post('/settings/toggle', [\App\Http\Controllers\NotificationSettingController::class, 'toggle'])->name('notifications.settings.toggle');
    });

    // ============================================
    // Financial Module - النظام المالي
    // ============================================

    // إيصالات القبض
    Route::prefix('receipts')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\PaymentReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/create', [\App\Http\Controllers\PaymentReceiptController::class, 'create'])->name('receipts.create');
        Route::post('/', [\App\Http\Controllers\PaymentReceiptController::class, 'store'])->name('receipts.store');
        Route::get('/report', [\App\Http\Controllers\PaymentReceiptController::class, 'report'])->name('receipts.report');
        Route::get('/{receipt}', [\App\Http\Controllers\PaymentReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/{receipt}/print', [\App\Http\Controllers\PaymentReceiptController::class, 'print'])->name('receipts.print');
        Route::post('/{receipt}/cancel', [\App\Http\Controllers\PaymentReceiptController::class, 'cancel'])->name('receipts.cancel');
        Route::post('/{receipt}/mark-bounced', [\App\Http\Controllers\PaymentReceiptController::class, 'markBounced'])->name('receipts.mark-bounced');
    });

    // سندات الصرف
    Route::prefix('vouchers')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\PaymentVoucherController::class, 'index'])->name('vouchers.index');
        Route::get('/pending-approval', [\App\Http\Controllers\PaymentVoucherController::class, 'pendingApproval'])->name('vouchers.pending-approval');
        Route::get('/create', [\App\Http\Controllers\PaymentVoucherController::class, 'create'])->name('vouchers.create');
        Route::post('/', [\App\Http\Controllers\PaymentVoucherController::class, 'store'])->name('vouchers.store');
        Route::get('/{voucher}', [\App\Http\Controllers\PaymentVoucherController::class, 'show'])->name('vouchers.show');
        Route::get('/{voucher}/print', [\App\Http\Controllers\PaymentVoucherController::class, 'print'])->name('vouchers.print');
        Route::post('/{voucher}/submit-for-approval', [\App\Http\Controllers\PaymentVoucherController::class, 'submitForApproval'])->name('vouchers.submit-for-approval');
        Route::post('/{voucher}/approve', [\App\Http\Controllers\PaymentVoucherController::class, 'approve'])->name('vouchers.approve');
        Route::post('/{voucher}/reject', [\App\Http\Controllers\PaymentVoucherController::class, 'reject'])->name('vouchers.reject');
        Route::post('/{voucher}/pay', [\App\Http\Controllers\PaymentVoucherController::class, 'pay'])->name('vouchers.pay');
        Route::post('/{voucher}/cancel', [\App\Http\Controllers\PaymentVoucherController::class, 'cancel'])->name('vouchers.cancel');
    });

    // كشوف الحسابات والأرصدة
    Route::prefix('statements')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\AccountStatementController::class, 'index'])->name('statements.index');
        Route::get('/summary', [\App\Http\Controllers\AccountStatementController::class, 'summary'])->name('statements.summary');
        Route::get('/contractor/{id}', [\App\Http\Controllers\AccountStatementController::class, 'contractorStatement'])->name('statements.contractor');
        Route::get('/supplier/{id}', [\App\Http\Controllers\AccountStatementController::class, 'supplierStatement'])->name('statements.supplier');
        Route::get('/employee/{id}', [\App\Http\Controllers\AccountStatementController::class, 'employeeStatement'])->name('statements.employee');
        Route::get('/print', [\App\Http\Controllers\AccountStatementController::class, 'printStatement'])->name('statements.print');
        Route::post('/opening-balance', [\App\Http\Controllers\AccountStatementController::class, 'setOpeningBalance'])->name('statements.opening-balance');
        Route::post('/recalculate/{id}', [\App\Http\Controllers\AccountStatementController::class, 'recalculate'])->name('statements.recalculate');
    });

    // الصندوق اليومي
    Route::prefix('cash')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\CashSummaryController::class, 'daily'])->name('cash.daily');
        Route::post('/close', [\App\Http\Controllers\CashSummaryController::class, 'close'])->name('cash.close');
        Route::get('/period-report', [\App\Http\Controllers\CashSummaryController::class, 'periodReport'])->name('cash.period-report');
        Route::get('/monthly-report', [\App\Http\Controllers\CashSummaryController::class, 'monthlyReport'])->name('cash.monthly-report');
        Route::post('/reopen/{date}', [\App\Http\Controllers\CashSummaryController::class, 'reopen'])->name('cash.reopen');
        Route::post('/recalculate/{date}', [\App\Http\Controllers\CashSummaryController::class, 'recalculate'])->name('cash.recalculate');
        Route::get('/print', [\App\Http\Controllers\CashSummaryController::class, 'print'])->name('cash.print');
    });

    // ============================================
    // Reports Routes - نظام التقارير
    // ============================================
    Route::prefix('reports')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

        // التقرير المالي الشامل (لمدير الشركة)
        Route::get('/financial', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-report.index');
        Route::get('/financial/print', [\App\Http\Controllers\FinancialReportController::class, 'print'])->name('financial-report.print');
        Route::get('/financial/export', [\App\Http\Controllers\FinancialReportController::class, 'export'])->name('financial-report.export');

        // تقارير الفرع
        Route::get('/orders', [\App\Http\Controllers\ReportController::class, 'orders'])->name('reports.orders');
        Route::get('/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/employees', [\App\Http\Controllers\ReportController::class, 'employees'])->name('reports.employees');
        Route::get('/advances', [\App\Http\Controllers\ReportController::class, 'advances'])->name('reports.advances');
        Route::get('/vehicles', [\App\Http\Controllers\ReportController::class, 'vehicles'])->name('reports.vehicles');
        Route::get('/losses', [\App\Http\Controllers\ReportController::class, 'losses'])->name('reports.losses');
        Route::get('/daily-cash', [\App\Http\Controllers\ReportController::class, 'dailyCash'])->name('reports.daily-cash');

        // تقارير الشركة
        Route::get('/branches-summary', [\App\Http\Controllers\ReportController::class, 'branchesSummary'])->name('reports.branches-summary');
        Route::get('/company-employees', [\App\Http\Controllers\ReportController::class, 'companyEmployees'])->name('reports.company-employees');
        Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/profit-loss', [\App\Http\Controllers\ReportController::class, 'profitLoss'])->name('reports.profit-loss');

        // تقارير السوبر أدمن
        Route::get('/companies', [\App\Http\Controllers\ReportController::class, 'companies'])->name('reports.companies');
        Route::get('/subscriptions', [\App\Http\Controllers\ReportController::class, 'subscriptions'])->name('reports.subscriptions');
        Route::get('/all-orders', [\App\Http\Controllers\ReportController::class, 'allOrders'])->name('reports.all-orders');
        Route::get('/activity', [\App\Http\Controllers\ReportController::class, 'activity'])->name('reports.activity');

        // طباعة وتصدير
        Route::get('/print/{type}', [\App\Http\Controllers\ReportController::class, 'print'])->name('reports.print');
        Route::get('/export/{type}', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    });

    // ============================================
    // Attendance Routes - نظام الحضور والانصراف
    // ============================================
    Route::prefix('attendance')->middleware(['auth'])->group(function () {
        // واجهة الموظف
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
        Route::get('/my-history', [AttendanceController::class, 'myHistory'])->name('attendance.myHistory');

        // واجهة الإدارة
        Route::get('/admin/dashboard', [AttendanceController::class, 'adminDashboard'])->name('attendance.admin.dashboard');
        Route::get('/admin/report', [AttendanceController::class, 'adminReport'])->name('attendance.admin.report');
        Route::put('/admin/{id}', [AttendanceController::class, 'adminUpdate'])->name('attendance.admin.update');
        Route::post('/admin/mark-absent', [AttendanceController::class, 'markAbsent'])->name('attendance.admin.markAbsent');
        Route::get('/admin/export', [AttendanceController::class, 'exportReport'])->name('attendance.admin.export');
    });
});
