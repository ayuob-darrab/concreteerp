<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    const UPDATED_AT = null; // لا يوجد updated_at

    protected $fillable = [
        'notification_id',
        'channel',
        'recipient',
        'status',
        'provider',
        'provider_response',
        'error_message',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';

    const CHANNEL_APP = 'app';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_EMAIL = 'email';

    /**
     * تسجيل محاولة إرسال
     */
    public static function log(
        string $notificationId,
        string $channel,
        string $recipient,
        string $status = self::STATUS_PENDING,
        ?string $provider = null
    ): self {
        return static::create([
            'notification_id' => $notificationId,
            'channel' => $channel,
            'recipient' => $recipient,
            'status' => $status,
            'provider' => $provider,
        ]);
    }

    /**
     * تحديث حالة الإرسال كناجح
     */
    public function markAsSent(?string $providerResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'provider_response' => $providerResponse,
        ]);
    }

    /**
     * تحديث حالة التسليم
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    /**
     * تحديث حالة الفشل
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * الحصول على سجلات إشعار معين
     */
    public static function getForNotification(string $notificationId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('notification_id', $notificationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * إحصائيات الإرسال لفترة معينة
     */
    public static function getStatistics(string $companyCode, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = static::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $stats = $query->selectRaw('
            channel,
            status,
            COUNT(*) as count
        ')->groupBy('channel', 'status')->get();

        $result = [];
        foreach ($stats as $stat) {
            if (!isset($result[$stat->channel])) {
                $result[$stat->channel] = [
                    'total' => 0,
                    'sent' => 0,
                    'delivered' => 0,
                    'failed' => 0,
                ];
            }
            $result[$stat->channel]['total'] += $stat->count;
            $result[$stat->channel][$stat->status] = $stat->count;
        }

        return $result;
    }
}
