<?php

namespace App\Http\Controllers;

use App\Models\PublicContactSettings;
use App\Models\PublicDisplayBlock;
use App\Models\PublicDisplayVideo;
use App\Services\PublicDisplayPageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DisplayPageAdminController extends Controller
{
    private function authorizeSa(): void
    {
        $user = auth()->user();
        if (! $user || $user->usertype_id !== 'SA' || $user->company_code !== 'SA' || $user->account_code !== 'SA') {
            abort(403, 'غير مصرح لك بالوصول');
        }
    }

    private function assertPageKey(string $pageKey): void
    {
        if (! in_array($pageKey, PublicDisplayPageService::PAGE_KEYS, true)) {
            abort(404);
        }
    }

    public function index()
    {
        $this->authorizeSa();

        $labels = [
            'landing' => 'الصفحة الرئيسية',
            'system_benefits' => 'فوائد النظام',
            'features' => 'المميزات',
            'about' => 'عن النظام',
            'contact' => 'تواصل معنا',
        ];

        return view('admin.display-pages.index', compact('labels'));
    }

    public function edit(string $pageKey)
    {
        $this->authorizeSa();
        $this->assertPageKey($pageKey);

        $pageLabels = [
            'landing' => 'الصفحة الرئيسية',
            'system_benefits' => 'فوائد النظام',
            'features' => 'المميزات',
            'about' => 'عن النظام',
            'contact' => 'تواصل معنا',
        ];

        if ($pageKey === 'contact') {
            $contactSettings = PublicContactSettings::singleton();

            return view('admin.display-pages.contact-settings', compact('pageKey', 'pageLabels', 'contactSettings'));
        }

        $blocks = PublicDisplayBlock::where('page_key', $pageKey)->orderBy('sort_order')->get();
        $videos = in_array($pageKey, ['landing', 'system_benefits'], true)
            ? PublicDisplayVideo::where('page_key', $pageKey)->orderBy('sort_order')->get()
            : collect();

        $blockTypeHints = $this->blockTypeHintsFor($pageKey);

        return view('admin.display-pages.edit', compact(
            'pageKey',
            'pageLabels',
            'blocks',
            'videos',
            'blockTypeHints'
        ));
    }

    public function updateContactSettings(Request $request)
    {
        $this->authorizeSa();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'intro_text' => ['nullable', 'string'],
            'hint_text' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:512'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
        ]);

        $settings = PublicContactSettings::singleton();
        $settings->update($validated);

        return redirect()->route('admin.display-pages.edit', 'contact')->with('success', 'تم حفظ صفحة التواصل.');
    }

    private function blockTypeHintsFor(string $pageKey): array
    {
        $common = [
            'plain' => 'فقرة نصية (عنوان اختياري + نص)',
            'bridge' => 'فقرة ربط قصيرة (نص توجيهي — مثل الرئيسية)',
            'kpi' => 'بطاقة مؤشر قصيرة (عنوان + سطر وصف)',
            'sidebar' => 'لوحة جانبية (عنوان + نص — مثلاً تواصل)',
            'sidebar_note' => 'ملاحظة صغيرة داخل اللوحة الجانبية',
            'card' => 'بطاقة مع أيقونة وقائمة نقاط',
            'highlight' => 'صندوق بعنوان وقائمة نقاط (مثل «باختصار»)',
            'footer_note' => 'ملاحظة ختامية في أسفل الصفحة',
            'long_text' => 'قسم بعنوان فرعي ونص طويل (فقرات متعددة)',
            'note' => 'تنبيه / ملاحظة',
            'section' => 'قسم «عن النظام» بعنوان ونص',
        ];

        return $common;
    }

    public function storeBlock(Request $request, string $pageKey)
    {
        $this->authorizeSa();
        $this->assertPageKey($pageKey);
        if ($pageKey === 'contact') {
            abort(404);
        }

        $data = $this->validateBlock($request);
        // توحيد كل كتل صفحات العرض: عنوان + نص فقط
        $data['block_type'] = 'plain';
        $data['page_key'] = $pageKey;
        $data['sort_order'] = (int) (PublicDisplayBlock::where('page_key', $pageKey)->max('sort_order') ?? 0) + 1;
        $data['is_active'] = true;

        PublicDisplayBlock::create($data);

        return redirect()->route('admin.display-pages.edit', $pageKey)->with('success', 'تمت إضافة الكتلة.');
    }

    public function updateBlock(Request $request, PublicDisplayBlock $publicDisplayBlock)
    {
        $this->authorizeSa();
        $this->assertPageKey($publicDisplayBlock->page_key);
        if ($publicDisplayBlock->page_key === 'contact') {
            abort(404);
        }

        $validated = $this->validateBlock($request);
        // توحيد كل كتل صفحات العرض: عنوان + نص فقط
        $validated['block_type'] = 'plain';
        $validated['is_active'] = $request->boolean('is_active');
        $publicDisplayBlock->update($validated);

        return redirect()->route('admin.display-pages.edit', $publicDisplayBlock->page_key)->with('success', 'تم حفظ التعديلات.');
    }

    public function destroyBlock(PublicDisplayBlock $publicDisplayBlock)
    {
        $this->authorizeSa();
        $this->assertPageKey($publicDisplayBlock->page_key);
        if ($publicDisplayBlock->page_key === 'contact') {
            abort(404);
        }
        $pageKey = $publicDisplayBlock->page_key;
        $publicDisplayBlock->delete();

        return redirect()->route('admin.display-pages.edit', $pageKey)->with('success', 'تم الحذف.');
    }

    public function moveBlock(Request $request, PublicDisplayBlock $publicDisplayBlock)
    {
        $this->authorizeSa();
        $this->assertPageKey($publicDisplayBlock->page_key);
        if ($publicDisplayBlock->page_key === 'contact') {
            abort(404);
        }
        $dir = $request->input('dir');
        if (! in_array($dir, ['up', 'down'], true)) {
            abort(422);
        }
        $rows = PublicDisplayBlock::where('page_key', $publicDisplayBlock->page_key)->orderBy('sort_order')->orderBy('id')->get();
        $idx = $rows->search(fn ($r) => $r->id === $publicDisplayBlock->id);
        if ($idx === false) {
            abort(404);
        }
        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= $rows->count()) {
            return back();
        }
        $a = $rows[$idx];
        $b = $rows[$swap];
        $tmp = $a->sort_order;
        $a->sort_order = $b->sort_order;
        $b->sort_order = $tmp;
        $a->save();
        $b->save();

        return back()->with('success', 'تم تعديل الترتيب.');
    }

    public function storeVideo(Request $request, string $pageKey)
    {
        $this->authorizeSa();
        if (! in_array($pageKey, ['landing', 'system_benefits'], true)) {
            abort(404);
        }
        $data = $request->validate([
            'youtube_url' => ['required', 'string', 'max:512'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $normalized = PublicDisplayVideo::normalizeYoutubeUrl($data['youtube_url']);
        if (! $normalized) {
            return back()->with('error', '⚠️ رابط يوتيوب غير صالح.')->withInput();
        }
        $newId = PublicDisplayVideo::extractYoutubeId($normalized);
        $exists = PublicDisplayVideo::where('page_key', $pageKey)->get()->contains(function ($v) use ($newId) {
            return $v->youtubeId() === $newId;
        });
        if ($exists) {
            return back()->with('error', '⚠️ هذا الفيديو مضاف مسبقاً (حسب الرابط/المعرّف).')->withInput();
        }
        $data['youtube_url'] = $normalized;

        $data['page_key'] = $pageKey;
        $data['sort_order'] = (int) (PublicDisplayVideo::where('page_key', $pageKey)->max('sort_order') ?? 0) + 1;
        $data['is_active'] = true;
        PublicDisplayVideo::create($data);

        return redirect()->route('admin.display-pages.edit', $pageKey)->with('success', 'تمت إضافة الفيديو.');
    }

    public function updateVideo(Request $request, PublicDisplayVideo $publicDisplayVideo)
    {
        $this->authorizeSa();
        if (! in_array($publicDisplayVideo->page_key, ['landing', 'system_benefits'], true)) {
            abort(404);
        }
        $validated = $request->validate([
            'youtube_url' => ['required', 'string', 'max:512'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $normalized = PublicDisplayVideo::normalizeYoutubeUrl($validated['youtube_url']);
        if (! $normalized) {
            return back()->with('error', '⚠️ رابط يوتيوب غير صالح.')->withInput();
        }
        $newId = PublicDisplayVideo::extractYoutubeId($normalized);
        $dup = PublicDisplayVideo::where('page_key', $publicDisplayVideo->page_key)
            ->where('id', '!=', $publicDisplayVideo->id)
            ->get()
            ->contains(fn ($v) => $v->youtubeId() === $newId);
        if ($dup) {
            return back()->with('error', '⚠️ هذا الفيديو مضاف مسبقاً (حسب الرابط/المعرّف).')->withInput();
        }

        $publicDisplayVideo->update([
            ...$validated,
            'youtube_url' => $normalized,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.display-pages.edit', $publicDisplayVideo->page_key)->with('success', 'تم حفظ الفيديو.');
    }

    public function destroyVideo(PublicDisplayVideo $publicDisplayVideo)
    {
        $this->authorizeSa();
        $pk = $publicDisplayVideo->page_key;
        $publicDisplayVideo->delete();

        return redirect()->route('admin.display-pages.edit', $pk)->with('success', 'تم حذف الفيديو.');
    }

    public function moveVideo(Request $request, PublicDisplayVideo $publicDisplayVideo)
    {
        $this->authorizeSa();
        $dir = $request->input('dir');
        if (! in_array($dir, ['up', 'down'], true)) {
            abort(422);
        }
        $rows = PublicDisplayVideo::where('page_key', $publicDisplayVideo->page_key)->orderBy('sort_order')->orderBy('id')->get();
        $idx = $rows->search(fn ($r) => $r->id === $publicDisplayVideo->id);
        if ($idx === false) {
            abort(404);
        }
        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= $rows->count()) {
            return back();
        }
        $a = $rows[$idx];
        $b = $rows[$swap];
        $t = $a->sort_order;
        $a->sort_order = $b->sort_order;
        $b->sort_order = $t;
        $a->save();
        $b->save();

        return back();
    }

    private function validateBlock(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
        ]);
    }
}
