<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    protected Invoice $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
        $daysOverdue = $this->invoice->days_overdue;

        return (new MailMessage)
            ->subject('فاتورة متأخرة - ' . $this->invoice->invoice_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تنبيه: لديك فاتورة متأخرة السداد')
            ->line('رقم الفاتورة: ' . $this->invoice->invoice_number)
            ->line('العميل: ' . $this->invoice->party_name)
            ->line('المبلغ المتبقي: ' . number_format($this->invoice->remaining_amount, 2) . ' د.ع')
            ->line('تاريخ الاستحقاق: ' . $this->invoice->due_date->format('Y-m-d'))
            ->line("متأخرة بـ {$daysOverdue} يوم")
            ->action('عرض الفاتورة', url('/invoices/' . $this->invoice->id))
            ->line('يرجى متابعة التحصيل.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'invoice_overdue',
            'title' => 'فاتورة متأخرة',
            'message' => "الفاتورة رقم {$this->invoice->invoice_number} متأخرة بـ {$this->invoice->days_overdue} يوم - المبلغ المتبقي: " . number_format($this->invoice->remaining_amount, 2) . ' د.ع',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'party_name' => $this->invoice->party_name,
            'remaining_amount' => $this->invoice->remaining_amount,
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'days_overdue' => $this->invoice->days_overdue,
            'url' => '/invoices/' . $this->invoice->id,
            'icon' => 'exclamation-triangle',
            'color' => 'red',
        ];
    }
}
