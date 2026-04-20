<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmployeeModuleAccess
{
    /**
     * فحص الوصول حسب نوع الموظف للوحدات الوظيفية.
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = auth()->user();

        // صلاحيات إدارية عليا: مسموح دائمًا
        if ($user->isSuperAdmin() || $user->isCompanyManager() || $user->isBranchManager()) {
            return $next($request);
        }

        $isAllowed = false;
        if ($module === 'engineer') {
            $isAllowed = $user->isEngineerEmployee();
        } elseif ($module === 'accountant') {
            $isAllowed = $user->isAccountantEmployee();
        } elseif ($module === 'warehouse') {
            $isAllowed = $user->isWarehouseEmployee();
            // استثناء: صفحة الموردين ضمن صلاحيات المحاسب
            if (!$isAllowed && $user->isAccountantEmployee()) {
                $isSuppliersPage =
                    $request->is('warehouse/addSupplier') ||
                    $request->is('warehouse/*&edit_Supplier/edit');

                $isSupplierCreate =
                    $request->isMethod('post') &&
                    $request->is('warehouse') &&
                    trim((string) $request->input('active', '')) === 'AddNewSupplier';

                $isSupplierUpdate =
                    in_array(strtolower($request->method()), ['put', 'patch'], true) &&
                    $request->is('warehouse/*') &&
                    trim((string) $request->input('active', '')) === 'UpdateSupplierinformation';

                $isAllowed = $isSuppliersPage || $isSupplierCreate || $isSupplierUpdate;
            }
        }

        if ($isAllowed) {
            return $next($request);
        }

        $message = 'غير مصرح لك بالوصول إلى هذا القسم.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        return redirect()->back()->with('error', $message);
    }
}
