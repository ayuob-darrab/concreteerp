<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * جميع الإشعارات
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId);

        // فلترة حسب الحالة
        if ($request->status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->status === 'read') {
            $query->whereNotNull('read_at');
        }

        // فلترة حسب النوع
        if ($request->type) {
            $query->where('notification_type', $request->type);
        }

        // فلترة حسب الأولوية
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // تحويل البيانات
        foreach ($notifications as $notification) {
            $notification->data = json_decode($notification->data, true);
        }

        $unreadCount = NotificationService::getUnreadCount($userId);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * الإشعارات الأخيرة للقائمة المنسدلة
     */
    public function dropdown()
    {
        $notifications = NotificationService::getUnread(auth()->id(), 5);
        $count = NotificationService::getUnreadCount(auth()->id());

        return response()->json([
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }

    /**
     * عدد غير المقروءة
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => NotificationService::getUnreadCount(auth()->id())
        ]);
    }

    /**
     * تحديد كمقروء
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->first();

        if (!$notification) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'الإشعار غير موجود'], 404);
            }
            return back()->with('error', 'الإشعار غير موجود');
        }

        NotificationService::markAsRead($id);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // إعادة التوجيه للرابط إن وجد
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'تم التحديد كمقروء');
    }

    /**
     * تحديد الكل كمقروء
     */
    public function markAllAsRead(Request $request)
    {
        $count = NotificationService::markAllAsRead(auth()->id());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'marked' => $count
            ]);
        }

        return back()->with('success', "تم تحديد {$count} إشعار كمقروء");
    }

    /**
     * حذف إشعار
     */
    public function destroy(Request $request, string $id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->first();

        if (!$notification) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'الإشعار غير موجود'], 404);
            }
            return back()->with('error', 'الإشعار غير موجود');
        }

        NotificationService::delete($id);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم حذف الإشعار');
    }

    /**
     * حذف جميع الإشعارات المقروءة
     */
    public function destroyRead(Request $request)
    {
        $count = DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', auth()->id())
            ->whereNotNull('read_at')
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => $count
            ]);
        }

        return back()->with('success', "تم حذف {$count} إشعار");
    }
}
