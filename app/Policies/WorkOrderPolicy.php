<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkOrderPolicy
{
    use HandlesAuthorization;

    /**
     * عرض قائمة الطلبات
     */
    public function viewAny(User $user)
    {
        return true; // كل المستخدمين المسجلين
    }

    /**
     * عرض طلب محدد
     */
    public function view(User $user, WorkOrder $workOrder)
    {
        // يمكن للجميع رؤية الطلبات
        return true;
    }

    /**
     * إنشاء طلب جديد
     */
    public function create(User $user)
    {
        // كل المستخدمين يمكنهم إنشاء طلبات
        return true;
    }

    /**
     * تحويل للمراجعة
     */
    public function review(User $user, WorkOrder $workOrder)
    {
        // منشئ الطلب أو المشرف
        return $workOrder->created_by === $user->id || $user->isAdmin();
    }

    /**
     * الموافقة على الطلب
     */
    public function approve(User $user, WorkOrder $workOrder)
    {
        // فقط المديرين والمشرفين
        return $user->isAdmin() || $user->isSupervisor();
    }

    /**
     * رفض الطلب
     */
    public function reject(User $user, WorkOrder $workOrder)
    {
        // فقط المديرين والمشرفين
        return $user->isAdmin() || $user->isSupervisor();
    }

    /**
     * جدولة الطلب
     */
    public function schedule(User $user, WorkOrder $workOrder)
    {
        // يجب أن يكون الطلب معتمداً
        if ($workOrder->status !== 'approved') {
            return false;
        }

        // المديرين ومنسقي التسليم
        return $user->isAdmin() || $user->isCoordinator();
    }

    /**
     * تغيير السعر
     */
    public function changePrice(User $user, WorkOrder $workOrder)
    {
        // فقط للطلبات التي لم تكتمل
        if (in_array($workOrder->status, ['completed', 'cancelled'])) {
            return false;
        }

        // المديرين فقط
        return $user->isAdmin();
    }

    /**
     * إضافة تنفيذ
     */
    public function addExecution(User $user, WorkOrder $workOrder)
    {
        // يجب أن يكون الطلب معتمداً أو مجدولاً أو قيد التنفيذ
        if (!in_array($workOrder->status, ['approved', 'scheduled', 'in_progress'])) {
            return false;
        }

        // المديرين ومنسقي التسليم
        return $user->isAdmin() || $user->isCoordinator();
    }

    /**
     * تحديث حالة التنفيذ
     */
    public function updateExecution(User $user, WorkOrder $workOrder)
    {
        // المديرين ومنسقي التسليم والسائقين
        return $user->isAdmin() || $user->isCoordinator() || $user->isDriver();
    }

    /**
     * إلغاء الطلب
     */
    public function cancel(User $user, WorkOrder $workOrder)
    {
        // لا يمكن إلغاء الطلبات المكتملة
        if (!$workOrder->canBeCancelled()) {
            return false;
        }

        // منشئ الطلب أو المديرين
        return $workOrder->created_by === $user->id || $user->isAdmin();
    }

    /**
     * تصدير التقارير
     */
    public function export(User $user)
    {
        // المديرين والمشرفين فقط
        return $user->isAdmin() || $user->isSupervisor();
    }

    /**
     * حذف الطلب (Soft Delete)
     */
    public function delete(User $user, WorkOrder $workOrder)
    {
        // المديرين فقط ويجب أن يكون الطلب ملغياً
        return $user->isAdmin() && $workOrder->status === 'cancelled';
    }

    /**
     * استرجاع الطلب المحذوف
     */
    public function restore(User $user, WorkOrder $workOrder)
    {
        // المديرين فقط
        return $user->isAdmin();
    }

    /**
     * حذف نهائي
     */
    public function forceDelete(User $user, WorkOrder $workOrder)
    {
        // المديرين العامين فقط
        return $user->isSuperAdmin();
    }
}


/**
 * يجب إضافة هذه الدوال في User Model:
 * 
 * public function isAdmin()
 * {
 *     return $this->user_type === 'admin' || $this->role === 'admin';
 * }
 * 
 * public function isSuperAdmin()
 * {
 *     return $this->user_type === 'super_admin';
 * }
 * 
 * public function isSupervisor()
 * {
 *     return $this->user_type === 'supervisor' || $this->role === 'supervisor';
 * }
 * 
 * public function isCoordinator()
 * {
 *     return $this->user_type === 'coordinator' || $this->role === 'coordinator';
 * }
 * 
 * public function isDriver()
 * {
 *     return $this->employee_type === 'driver';
 * }
 */


/**
 * تسجيل الـ Policy في AuthServiceProvider:
 * 
 * protected $policies = [
 *     WorkOrder::class => WorkOrderPolicy::class,
 * ];
 */
