<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Trait لمنع IDOR (Insecure Direct Object Reference)
 * 
 * يضمن أن المستخدم يمكنه فقط الوصول لبيانات شركته/فرعه
 */
trait AuthorizesCompanyAccess
{
    /**
     * التحقق من أن السجل ينتمي لشركة المستخدم
     */
    protected function authorizeCompanyAccess(Model $model, ?string $companyCodeColumn = 'company_code'): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('غير مصرح');
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $modelCompanyCode = $model->{$companyCodeColumn} ?? null;

        if ($modelCompanyCode !== $user->company_code) {
            $this->logUnauthorizedAccess($model, 'company_mismatch');
            throw new AccessDeniedHttpException('لا يمكنك الوصول لهذا السجل');
        }
    }

    /**
     * التحقق من أن السجل ينتمي لفرع المستخدم (إن كان مدير فرع)
     */
    protected function authorizeBranchAccess(Model $model, ?string $branchIdColumn = 'branch_id'): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('غير مصرح');
        }

        if ($user->isSuperAdmin() || $user->isCompanyManager()) {
            $this->authorizeCompanyAccess($model);

            return;
        }

        $this->authorizeCompanyAccess($model);

        $modelBranchId = $model->{$branchIdColumn} ?? null;

        if ($modelBranchId !== $user->branch_id) {
            $this->logUnauthorizedAccess($model, 'branch_mismatch');
            throw new AccessDeniedHttpException('لا يمكنك الوصول لهذا السجل');
        }
    }

    /**
     * فلترة الاستعلام حسب شركة المستخدم
     */
    protected function scopeToCompany($query, ?string $companyCodeColumn = 'company_code')
    {
        $user = Auth::user();

        if (!$user || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where($companyCodeColumn, $user->company_code);
    }

    /**
     * فلترة الاستعلام حسب فرع المستخدم
     */
    protected function scopeToBranch($query, ?string $branchIdColumn = 'branch_id')
    {
        $user = Auth::user();

        if (!$user || $user->isSuperAdmin() || $user->isCompanyManager()) {
            return $this->scopeToCompany($query);
        }

        return $this->scopeToCompany($query)->where($branchIdColumn, $user->branch_id);
    }

    /**
     * التحقق من ملكية السجل قبل التعديل/الحذف
     */
    protected function authorizeOwnership(Model $model, string $ownerColumn = 'user_id'): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('غير مصرح');
        }

        if ($user->isSuperAdmin() || $user->isCompanyManager()) {
            $this->authorizeCompanyAccess($model);

            return;
        }

        $modelOwnerId = $model->{$ownerColumn} ?? null;

        if ($modelOwnerId !== $user->id) {
            $this->logUnauthorizedAccess($model, 'ownership_mismatch');
            throw new AccessDeniedHttpException('لا يمكنك تعديل هذا السجل');
        }
    }

    /**
     * تسجيل محاولة الوصول غير المصرح
     */
    protected function logUnauthorizedAccess(Model $model, string $reason): void
    {
        logger()->warning('Unauthorized access attempt', [
            'user_id' => Auth::id(),
            'user_company' => Auth::user()?->company_code,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'reason' => $reason,
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
