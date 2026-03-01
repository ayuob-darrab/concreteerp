<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected WorkOrder $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // تحقق من إعدادات الإشعارات
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
        return (new MailMessage)
            ->subject('طلب جديد - ' . $this->order->order_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم استلام طلب جديد')
            ->line('رقم الطلب: ' . $this->order->order_number)
            ->line('العميل: ' . $this->order->customer_name)
            ->line('الكمية: ' . $this->order->quantity . ' م³')
            ->line('تاريخ التسليم: ' . $this->order->delivery_datetime->format('Y-m-d H:i'))
            ->action('عرض الطلب', url('/work-orders/' . $this->order->id))
            ->line('شكراً لاستخدامك نظامنا!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_order',
            'title' => 'طلب جديد',
            'message' => "تم استلام طلب جديد رقم {$this->order->order_number}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->customer_name,
            'quantity' => $this->order->quantity,
            'delivery_datetime' => $this->order->delivery_datetime->toISOString(),
            'url' => '/work-orders/' . $this->order->id,
            'icon' => 'shopping-cart',
            'color' => 'blue',
        ];
    }
}
