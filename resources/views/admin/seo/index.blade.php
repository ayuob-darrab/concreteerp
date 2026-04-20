@extends('layouts.app')

@section('page-title', 'إدارة SEO - تحسين محركات البحث')

@section('content')
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إدارة SEO - تحسين محركات البحث</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تحكم في العناوين والوصف والكلمات المفتاحية لتحسين ظهور الموقع في جوجل ومحركات البحث.</p>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900 dark:text-red-400 flex items-center gap-2">
                    {{ session('error') }}
                </div>
            @endif

            {{-- تبويبات الصفحات --}}
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500" id="global-tab" data-tabs-target="#global" type="button" role="tab" aria-controls="global" aria-selected="true">
                            <i class="fas fa-globe me-1"></i> الإعدادات العامة
                        </button>
                    </li>
                    @foreach($pageSettings as $key => $page)
                        <li class="me-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="{{ $key }}-tab" data-tabs-target="#{{ $key }}" type="button" role="tab" aria-controls="{{ $key }}" aria-selected="false">
                                {{ $pageLabels[$key] ?? $key }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div id="tabContent">
                {{-- تبويب الإعدادات العامة --}}
                <div class="block" id="global" role="tabpanel" aria-labelledby="global-tab">
                    <form action="{{ route('admin.seo.update') }}" method="POST">
                        @csrf

                        {{-- معاينة سريعة --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span class="text-2xl">🧾</span> معاينة سريعة (Preview)
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">يساعدك هذا الجدول على التأكد أن المحتوى جاهز للفهرسة والمشاركة.</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr class="text-gray-700 dark:text-gray-200">
                                            <th style="width: 220px">الوسم</th>
                                            <th>القيمة الحالية</th>
                                            <th style="width: 260px">ملاحظة لتحسين الظهور</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-gray-200">
                                        <tr>
                                            <td class="font-semibold">title</td>
                                            <td>{{ $seo->meta_title ?? '—' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">يفضّل 50–60 حرفاً مع كلمة أساسية.</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">meta description</td>
                                            <td>{{ $seo->meta_description ?? '—' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">يفضّل 150–160 حرفاً مع دعوة واضحة.</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">robots</td>
                                            <td>{{ $seo->robots ?? 'index, follow' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">للموقع العام: index, follow.</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">canonical</td>
                                            <td>{{ $seo->canonical_domain ?? 'تلقائي (الرابط الحالي)' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">ضع الدومين الرسمي عند الإنتاج (https).</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">og:title</td>
                                            <td>{{ $seo->og_title ?? '—' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">عنوان المشاركة في واتساب/فيسبوك.</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">og:description</td>
                                            <td>{{ $seo->og_description ?? '—' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">وصف قصير يطابق رسالة الموقع.</td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">og:image</td>
                                            <td>{{ $seo->og_image ?? '—' }}</td>
                                            <td class="text-xs text-gray-500 dark:text-gray-400">يفضّل 1200×630 مع شعار واضح.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- أساسيات SEO --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">🔍</span> أساسيات SEO (محركات البحث)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">اسم الموقع (Site Name)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يظهر في نتائج البحث وفي وسوم Open Graph كمصدر للموقع، ويساعد في التعريف بالعلامة.</p>
                                    <input type="text" name="site_name" value="{{ old('site_name', $seo->site_name ?? '') }}" maxlength="255"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="ConcreteERP - نظام إدارة شركات الخرسانة الجاهزة">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">العنوان (Meta Title)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يظهر في تبويب المتصفح وفي نتيجة جوجل كعنوان النقر؛ عنوان واضح يزيد نسبة الضغط. يُفضّل 50–60 حرفاً.</p>
                                    <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title ?? '') }}" maxlength="255"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="ConcreteERP | نظام ERP متكامل لإدارة شركات الخرسانة الجاهزة">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الوصف (Meta Description)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: النص الذي يظهر تحت الرابط في جوجل؛ وصف جذاب يوضح ماذا يقدم الموقع ويدفع المستخدم للنقر. يُفضّل 150–160 حرفاً.</p>
                                    <textarea name="meta_description" rows="3" maxlength="320"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="نظام ConcreteERP يساعد شركات الخرسانة الجاهزة في إدارة الطلبات، الأفرع، المقاولين، المخزون، الشحنات، الرواتب والحضور.">{{ old('meta_description', $seo->meta_description ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الكلمات المفتاحية (Meta Keywords)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: كلمات يعتمد عليها المحرك لفهم موضوع الموقع؛ تكتب مفصولة بفواصل وترتبط ببحث المستخدمين (مثل: خرسانة جاهزة، ERP، العراق).</p>
                                    <textarea name="meta_keywords" rows="2"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="نظام خرسانة جاهزة، ERP خرسانة، إدارة شركات خرسانة، طلبات خرسانة، مقاولين، خلطات خرسانية، العراق">{{ old('meta_keywords', $seo->meta_keywords ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Open Graph --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">📱</span> Open Graph (عند مشاركة الرابط)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">عنوان المشاركة (og:title)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: عند مشاركة الرابط في فيسبوك أو واتساب أو تويتر يظهر هذا العنوان؛ يحدد ماذا يرى الشخص قبل فتح الرابط.</p>
                                    <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title ?? '') }}" maxlength="255"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">وصف المشاركة (og:description)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: الوصف الذي يظهر عند مشاركة الرابط في الشبكات الاجتماعية؛ يوضح محتوى الصفحة ويزيد الرغبة في النقر.</p>
                                    <textarea name="og_description" rows="2"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('og_description', $seo->og_description ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">رابط صورة المشاركة (og:image)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: الصورة التي تظهر عند مشاركة الرابط؛ صورة واضحة (مقترح 1200×630) تجعل المنشور أوضح وأكثر جذباً.</p>
                                    <input type="url" name="og_image" value="{{ old('og_image', $seo->og_image ?? '') }}" maxlength="500"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="https://example.com/images/og-image.jpg">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">نوع المحتوى (og:type)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يحدد للمحركات ونشرات التواصل نوع الصفحة (موقع عام أو مقال) لتحسين عرض المشاركة.</p>
                                    <select name="og_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="website" {{ ($seo->og_type ?? '') == 'website' ? 'selected' : '' }}>website</option>
                                        <option value="article" {{ ($seo->og_type ?? '') == 'article' ? 'selected' : '' }}>article</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- تويتر وروبوتات --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">🐦</span> تويتر وروبوتات المحركات
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Twitter Card</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يحدد شكل عرض الرابط في تويتر (صورة كبيرة، ملخص، أو تطبيق) لتحسين المظهر عند المشاركة.</p>
                                    <select name="twitter_card" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="summary_large_image" {{ ($seo->twitter_card ?? '') == 'summary_large_image' ? 'selected' : '' }}>summary_large_image</option>
                                        <option value="summary" {{ ($seo->twitter_card ?? '') == 'summary' ? 'selected' : '' }}>summary</option>
                                        <option value="app" {{ ($seo->twitter_card ?? '') == 'app' ? 'selected' : '' }}>app</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">حساب تويتر @ (اختياري)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: ربط الموقع بحساب تويتر الرسمي؛ يظهر في بطاقة تويتر ويعزز الثقة.</p>
                                    <input type="text" name="twitter_site" value="{{ old('twitter_site', $seo->twitter_site ?? '') }}" maxlength="100"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="@ConcreteERP">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">تعليمات الروبوتات (robots)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: تخبر محركات البحث هل تُفهرس الصفحة وتتبع الروابط؛ "index, follow" يعني فهرسة عادية ومتابعة الروابط (الأنسب للموقع العام).</p>
                                    <select name="robots" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="index, follow" {{ ($seo->robots ?? '') == 'index, follow' ? 'selected' : '' }}>index, follow — فهرسة ومتابعة الروابط</option>
                                        <option value="noindex, follow" {{ ($seo->robots ?? '') == 'noindex, follow' ? 'selected' : '' }}>noindex, follow</option>
                                        <option value="index, nofollow" {{ ($seo->robots ?? '') == 'index, nofollow' ? 'selected' : '' }}>index, nofollow</option>
                                        <option value="noindex, nofollow" {{ ($seo->robots ?? '') == 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">النطاق الأساسي (Canonical)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يوحّد الرابط المعتمد للمحركات عند وجود أكثر من عنوان لنفس الصفحة (مثل http و https أو مع/بدون www)؛ اتركه فارغاً لاستخدام الرابط الحالي.</p>
                                    <input type="url" name="canonical_domain" value="{{ old('canonical_domain', $seo->canonical_domain ?? '') }}" maxlength="500"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="https://concreteerp.app">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">لغة الصفحة (locale)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: يحدد لغة المحتوى للمحركات (مثل ar_IQ للعربية العراقية) لتحسين عرض النتائج حسب لغة الباحث.</p>
                                    <input type="text" name="locale" value="{{ old('locale', $seo->locale ?? 'ar_IQ') }}" maxlength="10"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="ar_IQ">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">لغة بديلة (اختياري)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: كود لغة بديل (مثل ar) عند وجود نسخ بلغات متعددة؛ يساعد المحركات في ربط النسخ ببعضها.</p>
                                    <input type="text" name="locale_alternate" value="{{ old('locale_alternate', $seo->locale_alternate ?? '') }}" maxlength="50"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="ar">
                                </div>
                            </div>
                        </div>

                        {{-- بيانات منظمة ووسوم إضافية --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">📋</span> بيانات منظمة (Structured Data) ووسوم إضافية
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">وسوم meta إضافية (اختياري)</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: إدراج وسوم HTML إضافية في رأس الصفحة (مثل theme-color للون شريط المتصفح أو أي meta آخر) دون تعديل القالب.</p>
                                    <textarea name="extra_meta" rows="3"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-xs"
                                        placeholder='<meta name="theme-color" content="#0d9488">'>{{ old('extra_meta', $seo->extra_meta ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">JSON-LD (Organization Schema) - يظهر في جميع الصفحات</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: بيانات منظمة (مثل Organization أو WebSite) تساعد جوجل في عرض نتائج غنية (اسم، شعار، روابط) وتحسين الفهم للموقع.</p>
                                    <textarea name="structured_data" rows="6"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-xs"
                                        placeholder='{"@context":"https://schema.org","@type":"Organization","name":"ConcreteERP","url":"https://concreteerp.app"}'>{{ old('structured_data', $seo->structured_data ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info">
                            <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            حفظ الإعدادات العامة
                        </button>
                    </form>
                </div>

                {{-- تبويبات الصفحات --}}
                @foreach($pageSettings as $key => $page)
                    <div class="hidden" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
                        <form action="{{ route('admin.seo.update-page', $key) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <span class="text-2xl">📄</span> إعدادات صفحة: {{ $pageLabels[$key] ?? $key }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    رابط الصفحة: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $page->canonical_url ?? 'https://concreteerp.app/' . ($key === 'home' ? '' : $key) }}</code>
                                </p>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">عنوان الصفحة (Page Title)</label>
                                        <input type="text" name="page_title" value="{{ old('page_title', $page->page_title ?? '') }}" maxlength="255"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="عنوان الصفحة | ConcreteERP">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Title</label>
                                        <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}" maxlength="255"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Description <span class="text-gray-400">(150-160 حرف)</span></label>
                                        <textarea name="meta_description" rows="3" maxlength="320"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">عدد الأحرف: <span id="{{ $key }}_desc_count">{{ mb_strlen($page->meta_description ?? '') }}</span></p>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Keywords</label>
                                        <textarea name="meta_keywords" rows="2"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('meta_keywords', $page->meta_keywords ?? '') }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OG Title</label>
                                            <input type="text" name="og_title" value="{{ old('og_title', $page->og_title ?? '') }}" maxlength="255"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Canonical URL</label>
                                            <input type="url" name="canonical_url" value="{{ old('canonical_url', $page->canonical_url ?? '') }}" maxlength="500"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OG Description</label>
                                        <textarea name="og_description" rows="2"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('og_description', $page->og_description ?? '') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Schema Markup (JSON-LD) - خاص بهذه الصفحة</label>
                                        <textarea name="schema_markup" rows="8"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-xs">{{ old('schema_markup', $page->schema_markup ?? '') }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">أولوية Sitemap</label>
                                            <select name="sitemap_priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <option value="1.0" {{ ($page->sitemap_priority ?? 0.8) == 1.0 ? 'selected' : '' }}>1.0 (أعلى)</option>
                                                <option value="0.9" {{ ($page->sitemap_priority ?? 0.8) == 0.9 ? 'selected' : '' }}>0.9</option>
                                                <option value="0.8" {{ ($page->sitemap_priority ?? 0.8) == 0.8 ? 'selected' : '' }}>0.8 (افتراضي)</option>
                                                <option value="0.7" {{ ($page->sitemap_priority ?? 0.8) == 0.7 ? 'selected' : '' }}>0.7</option>
                                                <option value="0.6" {{ ($page->sitemap_priority ?? 0.8) == 0.6 ? 'selected' : '' }}>0.6</option>
                                                <option value="0.5" {{ ($page->sitemap_priority ?? 0.8) == 0.5 ? 'selected' : '' }}>0.5</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">تكرار التحديث (Changefreq)</label>
                                            <select name="sitemap_changefreq" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <option value="always" {{ ($page->sitemap_changefreq ?? 'monthly') == 'always' ? 'selected' : '' }}>always</option>
                                                <option value="hourly" {{ ($page->sitemap_changefreq ?? 'monthly') == 'hourly' ? 'selected' : '' }}>hourly</option>
                                                <option value="daily" {{ ($page->sitemap_changefreq ?? 'monthly') == 'daily' ? 'selected' : '' }}>daily</option>
                                                <option value="weekly" {{ ($page->sitemap_changefreq ?? 'monthly') == 'weekly' ? 'selected' : '' }}>weekly</option>
                                                <option value="monthly" {{ ($page->sitemap_changefreq ?? 'monthly') == 'monthly' ? 'selected' : '' }}>monthly (افتراضي)</option>
                                                <option value="yearly" {{ ($page->sitemap_changefreq ?? 'monthly') == 'yearly' ? 'selected' : '' }}>yearly</option>
                                                <option value="never" {{ ($page->sitemap_changefreq ?? 'monthly') == 'never' ? 'selected' : '' }}>never</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-info">
                                <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                حفظ إعدادات الصفحة
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- اقتراحات كلمات مفتاحية --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="text-2xl">📌</span> كلمات مفتاحية مقترحة (للمساعدة)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    استخدم 5–12 كلمة/عبارة قوية فقط، واجعلها قريبة من ما يبحث عنه العملاء.
                </p>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="text-gray-700 dark:text-gray-200">
                                <th style="width: 220px">مجال البحث</th>
                                <th>أمثلة كلمات/عبارات</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-200">
                            <tr>
                                <td class="font-semibold">خرسانة جاهزة</td>
                                <td class="text-sm text-gray-600 dark:text-gray-300">نظام إدارة مصانع الخرسانة الجاهزة، برنامج ERP للخرسانة، إدارة محطات الخرسانة، خلطات خرسانية، مضخات خرسانة</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">إدارة الأسطول</td>
                                <td class="text-sm text-gray-600 dark:text-gray-300">إدارة أسطول الميكسر، تتبع شاحنات الخرسانة، نظام GPS للخرسانة، إدارة السائقين</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">المحاسبة</td>
                                <td class="text-sm text-gray-600 dark:text-gray-300">نظام محاسبة مصنع خرسانة، فواتير الخرسانة، إدارة المقاولين، حسابات العملاء</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">نية الشراء</td>
                                <td class="text-sm text-gray-600 dark:text-gray-300">سعر برنامج إدارة مصانع الخرسانة، أفضل نظام ERP للخرسانة، طلب عرض ConcreteERP</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[role="tab"]');
            const tabPanels = document.querySelectorAll('[role="tabpanel"]');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-600', 'text-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                        t.classList.add('border-transparent');
                        t.setAttribute('aria-selected', 'false');
                    });

                    this.classList.add('border-blue-600', 'text-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                    this.classList.remove('border-transparent');
                    this.setAttribute('aria-selected', 'true');

                    tabPanels.forEach(panel => {
                        panel.classList.add('hidden');
                        panel.classList.remove('block');
                    });

                    const targetId = this.getAttribute('data-tabs-target');
                    const targetPanel = document.querySelector(targetId);
                    if (targetPanel) {
                        targetPanel.classList.remove('hidden');
                        targetPanel.classList.add('block');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
