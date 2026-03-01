<?php

namespace App\Notifications;

use App\Models\Check;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Check $check;
    protected string $oldStatus;
    protected string $newStatus;
    protected ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Check $check, string $oldStatus, string $newStatus, ?string $reason = null)
    {
        $this->check = $check;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // إرسال بريد إلكتروني للحالات المهمة
        $importantStatuses = ['rejected', 'collected', 'returned'];
        if (in_array($this->newStatus, $importantStatuses)) {
            if ($notifiable->notification_settings['email_enabled'] ?? true) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'deposited' => 'مودع في البنك',
            'collected' => 'تم التحصيل',
            'rejected' => 'مرفوض',
            'returned' => 'مرتجع',
            'cancelled' => 'ملغي',
            'endorsed' => 'مظهر',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $typeName = $this->check->check_type === 'incoming' ? 'الوارد' : 'الصادر';

        $mail = (new MailMessage)
            ->subject("تحديث حالة الشيك {$typeName} - " . $this->check->check_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تم تحديث حالة الشيك {$typeName}")
            ->line('رقم الشيك: ' . $this->check->check_number)
            ->line('البنك: ' . $this->check->bank_name)
            ->line('المبلغ: ' . number_format($this->check->amount, 2) . ' د.ع')
            ->line('الحالة الجديدة: ' . $newStatusLabel);

        if ($this->reason) {
            $mail->line('السبب: ' . $this->reason);
        }

        return $mail
            ->action('عرض الشيك', url('/checks/' . $this->check->id))
            ->line('شكراً لاستخدامك نظامنا!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'deposited' => 'مودع في البنك',
            'collected' => 'تم التحصيل',
            'rejected' => 'مرفوض',
            'returned' => 'مرتجع',
            'cancelled' => 'ملغي',
            'endorsed' => 'مظهر',
        ];

        $statusColors = [
            'pending' => 'yellow',
            'deposited' => 'blue',
            'collected' => 'green',
            'rejected' => 'red',
            'returned' => 'orange',
            'cancelled' => 'gray',
            'endorsed' => 'purple',
        ];

        $statusIcons = [
            'pending' => 'clock',
            'deposited' => 'building-library',
            'collected' => 'check-circle',
            'rejected' => 'x-circle',
            'returned' => 'arrow-uturn-left',
            'cancelled' => 'ban',
            'endorsed' => 'arrow-right-circle',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $typeName = $this->check->check_type === 'incoming' ? 'الوارد' : 'الصادر';

        return [
            'type' => 'check_status_changed',
            'title' => "تحديث حالة الشيك {$typeName}",
            'message' => "تم تحديث حالة الشيك رقم {$this->check->check_number} إلى: {$newStatusLabel}",
            'check_id' => $this->check->id,
            'check_number' => $this->check->check_number,
            'check_type' => $this->check->check_type,
            'bank_name' => $this->check->bank_name,
            'amount' => $this->check->amount,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'new_status_label' => $newStatusLabel,
            'reason' => $this->reason,
            'url' => '/checks/' . $this->check->id,
            'icon' => $statusIcons[$this->newStatus] ?? 'credit-card',
            'color' => $statusColors[$this->newStatus] ?? 'gray',
        ];
    }
}
