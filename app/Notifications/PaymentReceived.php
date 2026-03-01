<?php

namespace App\Notifications;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected Receipt $receipt;

    /**
     * Create a new notification instance.
     */
    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
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
        $typeName = $this->receipt->receipt_type === 'receipt' ? 'قبض' : 'صرف';

        return (new MailMessage)
            ->subject("سند {$typeName} جديد - " . $this->receipt->receipt_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تم تسجيل سند {$typeName} جديد")
            ->line('رقم السند: ' . $this->receipt->receipt_number)
            ->line('المبلغ: ' . number_format($this->receipt->amount, 2) . ' د.ع')
            ->line('التاريخ: ' . $this->receipt->receipt_date->format('Y-m-d'))
            ->action('عرض السند', url('/receipts/' . $this->receipt->id))
            ->line('شكراً لاستخدامك نظامنا!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $typeName = $this->receipt->receipt_type === 'receipt' ? 'قبض' : 'صرف';

        return [
            'type' => 'payment_received',
            'title' => "سند {$typeName} جديد",
            'message' => "تم تسجيل سند {$typeName} رقم {$this->receipt->receipt_number} بمبلغ " . number_format($this->receipt->amount, 2) . ' د.ع',
            'receipt_id' => $this->receipt->id,
            'receipt_number' => $this->receipt->receipt_number,
            'receipt_type' => $this->receipt->receipt_type,
            'amount' => $this->receipt->amount,
            'receipt_date' => $this->receipt->receipt_date->format('Y-m-d'),
            'url' => '/receipts/' . $this->receipt->id,
            'icon' => $this->receipt->receipt_type === 'receipt' ? 'arrow-down-circle' : 'arrow-up-circle',
            'color' => $this->receipt->receipt_type === 'receipt' ? 'green' : 'red',
        ];
    }
}
