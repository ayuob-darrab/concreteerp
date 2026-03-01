<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * إرسال إشعار لمستخدم واحد
     */
    public static function send(
        string $type,
        int $userId,
        array $data = [],
        ?string $actionUrl = null,
        ?string $companyCode = null,
        ?int $branchId = null
    ): ?string {
        $template = NotificationTemplate::getByType($type);
        if (!$template) {
            return null;
        }

        // تجهيز المحتوى
        $rendered = $template->render($data);

        // الحصول على إعدادات المستخدم
        $userSettings = NotificationSetting::getForUser($userId, $type);
        $channels = $userSettings
            ? $userSettings->getEnabledChannels()
            : $template->default_channels;

        // إنشاء الإشعار
        $notificationId = Str::uuid()->toString();

        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        // إنشاء في قاعدة البيانات
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'App\\Notifications\\SystemNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $userId,
            'company_code' => $companyCode ?? $user->company_code,
            'branch_id' => $branchId,
            'notification_type' => $type,
            'priority' => $rendered['priority'],
            'channels' => json_encode($channels),
            'data' => json_encode([
                'title' => $rendered['title'],
                'body' => $rendered['body'],
                'data' => $data,
            ]),
            'action_url' => $actionUrl ?? ($rendered['action_route'] ? self::generateActionUrl($rendered['action_route'], $data) : null),
            'icon' => $rendered['icon'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إرسال عبر القنوات المختلفة
        $sentChannels = [];
        foreach ($channels as $channel) {
            try {
                $sent = self::sendViaChannel($channel, $user, $rendered, $notificationId);
                if ($sent) {
                    $sentChannels[] = $channel;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send notification via {$channel}: " . $e->getMessage());
            }
        }

        // تحديث القنوات المرسلة
        DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['sent_channels' => json_encode($sentChannels)]);

        return $notificationId;
    }

    /**
     * إرسال لمجموعة مستخدمين
     */
    public static function sendToUsers(
        string $type,
        array $userIds,
        array $data = [],
        ?string $actionUrl = null,
        ?string $companyCode = null
    ): array {
        $sentIds = [];
        foreach ($userIds as $userId) {
            $id = self::send($type, $userId, $data, $actionUrl, $companyCode);
            if ($id) {
                $sentIds[] = $id;
            }
        }
        return $sentIds;
    }

    /**
     * إرسال لجميع مستخدمي دور معين
     */
    public static function sendToRole(
        string $type,
        string $role,
        array $data = [],
        ?string $companyCode = null,
        ?int $branchId = null
    ): array {
        $query = User::where('role', $role);

        if ($companyCode) {
            $query->where('company_code', $companyCode);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $userIds = $query->pluck('id')->toArray();
        return self::sendToUsers($type, $userIds, $data, null, $companyCode);
    }

    /**
     * إرسال عبر قناة معينة
     */
    protected static function sendViaChannel(string $channel, User $user, array $content, string $notificationId): bool
    {
        switch ($channel) {
            case 'app':
                // الإشعار موجود بالفعل في قاعدة البيانات
                NotificationLog::log($notificationId, 'app', $user->email ?? $user->id, NotificationLog::STATUS_DELIVERED);
                return true;

            case 'sms':
                return self::sendSms($user, $content, $notificationId);

            case 'whatsapp':
                return self::sendWhatsApp($user, $content, $notificationId);

            case 'email':
                return self::sendEmail($user, $content, $notificationId);

            default:
                return false;
        }
    }

    /**
     * إرسال SMS
     */
    protected static function sendSms(User $user, array $content, string $notificationId): bool
    {
        if (empty($user->phone)) {
            return false;
        }

        $log = NotificationLog::log($notificationId, 'sms', $user->phone, NotificationLog::STATUS_PENDING, 'twilio');

        // TODO: تكامل مع خدمة SMS (Twilio أو غيرها)
        // مثال:
        // try {
        //     $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
        //     $twilio->messages->create($user->phone, [
        //         'from' => config('services.twilio.from'),
        //         'body' => $content['title'] . "\n" . $content['body']
        //     ]);
        //     $log->markAsSent();
        //     return true;
        // } catch (\Exception $e) {
        //     $log->markAsFailed($e->getMessage());
        //     return false;
        // }

        $log->markAsSent('SMS service not configured');
        return true;
    }

    /**
     * إرسال WhatsApp
     */
    protected static function sendWhatsApp(User $user, array $content, string $notificationId): bool
    {
        if (empty($user->phone)) {
            return false;
        }

        $log = NotificationLog::log($notificationId, 'whatsapp', $user->phone, NotificationLog::STATUS_PENDING, 'whatsapp_api');

        // TODO: تكامل مع WhatsApp Business API
        // مثال مع Twilio WhatsApp:
        // try {
        //     $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
        //     $twilio->messages->create('whatsapp:' . $user->phone, [
        //         'from' => 'whatsapp:' . config('services.twilio.whatsapp_from'),
        //         'body' => $content['title'] . "\n" . $content['body']
        //     ]);
        //     $log->markAsSent();
        //     return true;
        // } catch (\Exception $e) {
        //     $log->markAsFailed($e->getMessage());
        //     return false;
        // }

        $log->markAsSent('WhatsApp service not configured');
        return true;
    }

    /**
     * إرسال Email
     */
    protected static function sendEmail(User $user, array $content, string $notificationId): bool
    {
        if (empty($user->email)) {
            return false;
        }

        $log = NotificationLog::log($notificationId, 'email', $user->email, NotificationLog::STATUS_PENDING, 'smtp');

        try {
            Mail::raw($content['body'], function ($message) use ($user, $content) {
                $message->to($user->email)
                    ->subject($content['title']);
            });
            $log->markAsSent();
            return true;
        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * توليد رابط الإجراء
     */
    protected static function generateActionUrl(string $routeName, array $data): ?string
    {
        try {
            // استخراج ID من البيانات
            $id = $data['id'] ?? $data['order_id'] ?? $data['shipment_id'] ?? null;
            if ($id && Route::has($routeName)) {
                return route($routeName, $id);
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * تحديد كمقروء
     */
    public static function markAsRead(string $notificationId): bool
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]) > 0;
    }

    /**
     * تحديد جميع إشعارات المستخدم كمقروءة
     */
    public static function markAllAsRead(int $userId): int
    {
        return DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * الحصول على عدد غير المقروءة
     */
    public static function getUnreadCount(int $userId): int
    {
        return DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * الحصول على الإشعارات غير المقروءة
     */
    public static function getUnread(int $userId, int $limit = 10): \Illuminate\Support\Collection
    {
        return DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $notification->data = json_decode($notification->data, true);
                return $notification;
            });
    }

    /**
     * الحصول على جميع إشعارات المستخدم
     */
    public static function getForUser(int $userId, int $perPage = 20)
    {
        return DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * حذف إشعار
     */
    public static function delete(string $notificationId): bool
    {
        return DB::table('notifications')->where('id', $notificationId)->delete() > 0;
    }

    /**
     * حذف الإشعارات المنتهية الصلاحية
     */
    public static function deleteExpired(): int
    {
        return DB::table('notifications')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();
    }
}
