<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ClearExpiredSessions extends Command
{
    protected $signature = 'sessions:clear-expired';
    protected $description = 'مسح الجلسات المنتهية للمستخدمين الذين لم ينشطوا لفترة طويلة';

    public function handle()
    {
        $this->info('جاري مسح الجلسات المنتهية...');

        // البحث عن المستخدمين المسجلين دخول ولديهم last_activity_at
        $users = User::where('is_logged_in', true)
            ->whereNotNull('last_activity_at')
            ->get();

        $clearedCount = 0;

        foreach ($users as $user) {
            $timeoutMinutes = $user->session_timeout_minutes ?? 480; // 8 ساعات افتراضي
            $lastActivity = Carbon::parse($user->last_activity_at);

            // هل مر وقت أكثر من المسموح؟
            if (Carbon::now()->diffInMinutes($lastActivity) > $timeoutMinutes) {
                // إلغاء تفعيل الجلسة
                $user->is_logged_in = false;
                $user->device_fingerprint = null;
                $user->last_activity_at = null;
                $user->current_session_id = null;
                $user->save();

                $clearedCount++;
                $this->line("- تم مسح جلسة: {$user->email}");
            }
        }

        $this->info("تم مسح {$clearedCount} جلسة منتهية.");

        return 0;
    }
}
