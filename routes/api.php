<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContractorApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\CheckApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// Contractor API Routes - واجهة برمجة المقاولين
// ============================================
Route::middleware('auth:sanctum')->prefix('contractors')->group(function () {
    // المقاولين
    Route::get('/', [ContractorApiController::class, 'index']);
    Route::get('/search', [ContractorApiController::class, 'search']);
    Route::get('/{contractor}', [ContractorApiController::class, 'show']);
    Route::get('/{contractor}/account', [ContractorApiController::class, 'account']);
    Route::get('/{contractor}/statistics', [ContractorApiController::class, 'statistics']);
    Route::get('/{contractor}/transactions', [ContractorApiController::class, 'transactions']);

    // الفواتير
    Route::get('/{contractor}/invoices', [InvoiceApiController::class, 'contractorInvoices']);
    Route::get('/invoices/{invoice}', [InvoiceApiController::class, 'show']);
    Route::get('/invoices/overdue', [InvoiceApiController::class, 'overdue']);
    Route::get('/invoices/statistics', [InvoiceApiController::class, 'statistics']);

    // الشيكات
    Route::get('/{contractor}/checks', [CheckApiController::class, 'contractorChecks']);
    Route::get('/checks/{check}', [CheckApiController::class, 'show']);
    Route::get('/checks/due-today', [CheckApiController::class, 'dueToday']);
    Route::get('/checks/due-week', [CheckApiController::class, 'dueThisWeek']);
    Route::get('/checks/overdue', [CheckApiController::class, 'overdue']);
    Route::get('/checks/statistics', [CheckApiController::class, 'statistics']);
});
