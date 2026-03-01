<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected WorkOrder $order;
    protected string $oldStatus;
    protected string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkOrder $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notification_settings['email_enabled'] ?? true) {
            $channels[] = 'mail';
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
            'confirmed' => 'مؤكد',
            'in_production' => 'قيد التصنيع',
            'ready' => 'جاهز للتسليم',
            'in_delivery' => 'قيد التوصيل',
            'delivered' => 'تم التسليم',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return (new MailMessage)
            ->subject('تحديث حالة الطلب - ' . $this->order->order_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تحديث حالة طلبك')
            ->line('رقم الطلب: ' . $this->order->order_number)
            ->line('الحالة الجديدة: ' . $newStatusLabel)
            ->action('عرض الطلب', url('/work-orders/' . $this->order->id))
            ->line('شكراً لاستخدامك نظامنا!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'in_production' => 'قيد التصنيع',
            'ready' => 'جاهز للتسليم',
            'in_delivery' => 'قيد التوصيل',
            'delivered' => 'تم التسليم',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        $statusColors = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'in_production' => 'indigo',
            'ready' => 'cyan',
            'in_delivery' => 'purple',
            'delivered' => 'green',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return [
            'type' => 'order_status_changed',
            'title' => 'تحديث حالة الطلب',
            'message' => "تم تحديث حالة الطلب رقم {$this->order->order_number} إلى: " . ($statusLabels[$this->newStatus] ?? $this->newStatus),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'new_status_label' => $statusLabels[$this->newStatus] ?? $this->newStatus,
            'url' => '/work-orders/' . $this->order->id,
            'icon' => 'refresh-cw',
            'color' => $statusColors[$this->newStatus] ?? 'gray',
        ];
    }
}
