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

            <form action="{{ route('admin.seo.update') }}" method="POST">
                @csrf

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

                {{-- Open Graph (فيسبوك ومشاركة اجتماعية) --}}
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
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">JSON-LD أو بيانات منظمة (اختياري)</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">الفائدة: بيانات منظمة (مثل Organization أو WebSite) تساعد جوجل في عرض نتائج غنية (اسم، شعار، روابط) وتحسين الفهم للموقع.</p>
                            <textarea name="structured_data" rows="6"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-xs"
                                placeholder='{"@context":"https://schema.org","@type":"Organization","name":"ConcreteERP","url":"https://concreteerp.app"}'>{{ old('structured_data', $seo->structured_data ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info">
                    <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    حفظ إعدادات SEO
                </button>
            </form>
        </div>
    </div>
@endsection
