<?php

namespace App\Notifications;

use App\Models\Check;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class CheckDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $checks;
    protected int $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $checks, int $daysRemaining = 0)
    {
        $this->checks = $checks;
        $this->daysRemaining = $daysRemaining;
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
        $totalAmount = $this->checks->sum('amount');
        $count = $this->checks->count();

        $subject = $this->daysRemaining === 0
            ? "شيكات مستحقة اليوم ({$count})"
            : "تذكير: شيكات مستحقة خلال {$this->daysRemaining} أيام ({$count})";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('مرحباً ' . $notifiable->name);

        if ($this->daysRemaining === 0) {
            $mail->line("لديك {$count} شيكات مستحقة اليوم");
        } else {
            $mail->line("تذكير: لديك {$count} شيكات مستحقة خلال {$this->daysRemaining} أيام");
        }

        $mail->line('إجمالي المبلغ: ' . number_format($totalAmount, 2) . ' د.ع');

        // إضافة قائمة بالشيكات
        $mail->line('---');
        foreach ($this->checks->take(5) as $check) {
            $mail->line("• شيك رقم {$check->check_number} - {$check->bank_name} - " . number_format($check->amount, 2) . ' د.ع');
        }

        if ($count > 5) {
            $mail->line("... و " . ($count - 5) . " شيكات أخرى");
        }

        return $mail
            ->action('عرض الشيكات المستحقة', url('/checks/due-today'))
            ->line('يرجى اتخاذ الإجراءات اللازمة.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $totalAmount = $this->checks->sum('amount');
        $count = $this->checks->count();

        $title = $this->daysRemaining === 0
            ? "شيكات مستحقة اليوم"
            : "شيكات مستحقة خلال {$this->daysRemaining} أيام";

        $message = $this->daysRemaining === 0
            ? "لديك {$count} شيكات مستحقة اليوم بإجمالي " . number_format($totalAmount, 2) . ' د.ع'
            : "لديك {$count} شيكات مستحقة خلال {$this->daysRemaining} أيام بإجمالي " . number_format($totalAmount, 2) . ' د.ع';

        return [
            'type' => 'check_due_reminder',
            'title' => $title,
            'message' => $message,
            'checks_count' => $count,
            'total_amount' => $totalAmount,
            'days_remaining' => $this->daysRemaining,
            'check_ids' => $this->checks->pluck('id')->toArray(),
            'url' => $this->daysRemaining === 0 ? '/checks/due-today' : '/checks/due-this-week',
            'icon' => 'bell',
            'color' => $this->daysRemaining === 0 ? 'red' : 'yellow',
        ];
    }
}
