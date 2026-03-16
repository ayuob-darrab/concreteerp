<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMonthlySubscriptionInvoiceNotifications extends Command
{
    protected $signature = 'subscriptions:send-monthly-invoice-notifications';
    protected $description = 'إرسال إشعار شهري بالفاتورة للشركة المشتركة والشركة المالكة (كل أول شهر)';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // جميع الاشتراكات النشطة الحالية (لم تنتهِ)
        $subscriptions = CompanySubscription::with('company')
            ->where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->get();

        $sentCount = 0;
        $systemUserId = User::where('usertype_id', 'SA')->where('company_code', 'SA')->value('id');

        foreach ($subscriptions as $subscription) {
            $company = $subscription->company;
            $companyName = $company ? $company->name : $subscription->company_code;

            $planLabels = [
                'monthly' => 'شهري (بعدد المستخدمين)',
                'yearly' => 'سنوي (بعدد المستخدمين)',
                'percentage' => 'نسبة من الطلبات',
                'hybrid' => 'هجين',
                'trial' => 'تجريبي',
            ];
            $planLabel = $planLabels[$subscription->plan_type] ?? $subscription->plan_type;

            $periodText = $subscription->start_date && $subscription->end_date
                ? $subscription->start_date->format('Y-m-d') . ' إلى ' . $subscription->end_date->format('Y-m-d')
                : 'غير محدد';

            $amountText = $subscription->total_amount
                ? number_format((float) $subscription->total_amount, 0) . ' دينار'
                : 'حسب الاستخدام';

            $message = "إشعار فاتورة اشتراك - شهر {$now->translatedFormat('F Y')}\n\n";
            $message .= "الشركة: {$companyName}\n";
            $message .= "نوع الخطة: {$planLabel}\n";
            $message .= "الفترة: {$periodText}\n";
            $message .= "المبلغ حسب الخطة: {$amountText}\n";
            $message .= "حالة السداد: " . ($subscription->payment_status === 'paid' ? 'مسدد' : ($subscription->payment_status === 'partial' ? 'جزئي' : 'معلق')) . "\n\n";
            $message .= "هذا الإشعار يُرسل تلقائياً في أول كل شهر وفق الخطة المتفق عليها.";

            $title = "فاتورة اشتراك - {$companyName} - " . $now->translatedFormat('F Y');

            // إشعار للشركة المشتركة
            Notification::create([
                'company_code' => $subscription->company_code,
                'branch_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'info',
                'related_type' => 'subscription',
                'related_id' => $subscription->id,
                'is_read' => false,
                'sent_by' => $systemUserId,
            ]);

            // إشعار للشركة المالكة (النظام - السوبر أدمن)
            Notification::create([
                'company_code' => 'SA',
                'branch_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'info',
                'related_type' => 'subscription',
                'related_id' => $subscription->id,
                'is_read' => false,
                'sent_by' => $systemUserId,
            ]);

            $sentCount += 2;
        }

        $this->info("تم إرسال {$sentCount} إشعاراً لـ " . $subscriptions->count() . " اشتراك نشط.");
        return 0;
    }
}
