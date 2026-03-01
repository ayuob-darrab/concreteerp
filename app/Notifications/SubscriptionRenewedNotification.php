<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CompanySubscription;
use App\Models\Company;

class SubscriptionRenewedNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $subscription;
    protected $actionType;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Company $company, CompanySubscription $subscription, string $actionType = 'renewed')
    {
        $this->company = $company;
        $this->subscription = $subscription;
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $planNames = [
            'monthly' => 'شهري',
            'yearly' => 'سنوي',
            'trial' => 'تجريبي',
            'percentage' => 'نسبة من الطلبات',
            'hybrid' => 'هجين',
        ];

        $planName = $planNames[$this->subscription->plan_type] ?? $this->subscription->plan_type;

        return (new MailMessage)
            ->subject('تم تجديد اشتراككم بنجاح')
            ->greeting("مرحباً شركة {$this->company->name}")
            ->line('تم تجديد اشتراككم بنجاح!')
            ->line("نوع الاشتراك: {$planName}")
            ->line("تاريخ البداية: " . $this->subscription->start_date->format('Y/m/d'))
            ->line("تاريخ النهاية: " . ($this->subscription->end_date ? $this->subscription->end_date->format('Y/m/d') : 'غير محدد'))
            ->action('عرض التفاصيل', url('/'))
            ->line('شكراً لثقتكم بنا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $planNames = [
            'monthly' => 'شهري',
            'yearly' => 'سنوي',
            'trial' => 'تجريبي',
            'percentage' => 'نسبة من الطلبات',
            'hybrid' => 'هجين',
        ];

        $actionMessages = [
            'created' => 'تم إنشاء اشتراك جديد',
            'renewed' => 'تم تجديد الاشتراك',
            'extended' => 'تم تمديد الاشتراك',
            'payment' => 'تم تسجيل دفعة',
        ];

        return [
            'company_code' => $this->company->code,
            'company_name' => $this->company->name,
            'subscription_id' => $this->subscription->id,
            'plan_type' => $this->subscription->plan_type,
            'plan_name' => $planNames[$this->subscription->plan_type] ?? $this->subscription->plan_type,
            'action_type' => $this->actionType,
            'action_message' => $actionMessages[$this->actionType] ?? 'تحديث الاشتراك',
            'start_date' => $this->subscription->start_date->format('Y/m/d'),
            'end_date' => $this->subscription->end_date ? $this->subscription->end_date->format('Y/m/d') : null,
            'base_fee' => $this->subscription->base_fee,
            'duration_quantity' => $this->subscription->duration_quantity,
        ];
    }
}
