<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyNotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات للشركة
     */
    public function index(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        $query = Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'new') {
                $query->where('is_read', false);
            } else {
                $query->where('is_read', true);
            }
        }

        // فلترة حسب النوع
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(15);

        // إحصائيات
        $stats = [
            'total' => Notification::where(function ($q) use ($companyCode) {
                $q->where('company_code', $companyCode)->orWhere('company_code', 'ALL');
            })->count(),
            'new' => Notification::where(function ($q) use ($companyCode) {
                $q->where('company_code', $companyCode)->orWhere('company_code', 'ALL');
            })->where('is_read', false)->count(),
            'read' => Notification::where(function ($q) use ($companyCode) {
                $q->where('company_code', $companyCode)->orWhere('company_code', 'ALL');
            })->where('is_read', true)->count(),
        ];

        return view('company.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * عرض تفاصيل إشعار
     */
    public function show($id)
    {
        $companyCode = Auth::user()->company_code;

        $notification = Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->findOrFail($id);

        return response()->json([
            'success' => true,
            'notification' => $notification
        ]);
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead($id)
    {
        $companyCode = Auth::user()->company_code;

        $notification = Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->findOrFail($id);

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $companyCode = Auth::user()->company_code;

        Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد جميع الإشعارات كمقروءة'
        ]);
    }

    /**
     * الحصول على عدد الإشعارات الجديدة (للأيقونة)
     */
    public function getNewCount()
    {
        $companyCode = Auth::user()->company_code;

        $count = Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->where('is_read', false)->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * الحصول على آخر الإشعارات (للـ Dropdown)
     */
    public function getRecent()
    {
        $companyCode = Auth::user()->company_code;

        $notifications = Notification::where(function ($q) use ($companyCode) {
            $q->where('company_code', $companyCode)
                ->orWhere('company_code', 'ALL');
        })->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
