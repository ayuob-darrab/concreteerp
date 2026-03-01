<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    /**
     * صفحة إعدادات الإشعارات
     */
    public function index()
    {
        $settings = NotificationSetting::getAllForUser(auth()->id());
        $templates = NotificationTemplate::getGrouped();

        return view('notifications.settings.index', compact('settings', 'templates'));
    }

    /**
     * حفظ الإعدادات
     */
    public function update(Request $request)
    {
        $userId = auth()->id();
        $settings = $request->input('settings', []);

        foreach ($settings as $type => $channels) {
            NotificationSetting::saveForUser($userId, $type, $channels);
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    /**
     * تبديل قناة لنوع معين (AJAX)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'channel' => 'required|in:app,sms,whatsapp,email',
            'enabled' => 'required|boolean',
        ]);

        $userId = auth()->id();
        $setting = NotificationSetting::firstOrCreate(
            ['user_id' => $userId, 'notification_type' => $request->type],
            [
                'app_enabled' => true,
                'sms_enabled' => false,
                'whatsapp_enabled' => false,
                'email_enabled' => false,
            ]
        );

        $setting->toggleChannel($request->channel, $request->enabled);

        return response()->json([
            'success' => true,
            'setting' => $setting->fresh(),
        ]);
    }

    /**
     * تفعيل جميع الإشعارات لقناة معينة
     */
    public function enableAll(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:app,sms,whatsapp,email',
        ]);

        $userId = auth()->id();
        $channel = $request->channel;
        $field = "{$channel}_enabled";

        $templates = NotificationTemplate::where('is_active', true)->get();

        foreach ($templates as $template) {
            NotificationSetting::updateOrCreate(
                ['user_id' => $userId, 'notification_type' => $template->type],
                [$field => true]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * تعطيل جميع الإشعارات لقناة معينة
     */
    public function disableAll(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:app,sms,whatsapp,email',
        ]);

        $userId = auth()->id();
        $channel = $request->channel;
        $field = "{$channel}_enabled";

        NotificationSetting::where('user_id', $userId)
            ->update([$field => false]);

        return response()->json(['success' => true]);
    }
}
