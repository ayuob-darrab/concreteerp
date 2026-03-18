@extends('layouts.app')

@section('page-title', 'الإعدادات العامة')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إعدادات النظام</h1>
            </div>

            @if (session('success'))
                <div
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div
                    class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <!-- General Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        الإعدادات العامة
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">اسم النظام</label>
                            <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">البريد الإلكتروني
                                للدعم</label>
                            <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="support@example.com">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">المنطقة
                                الزمنية</label>
                            <select name="timezone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Asia/Baghdad"
                                    {{ ($settings['timezone'] ?? '') == 'Asia/Baghdad' ? 'selected' : '' }}>بغداد (GMT+3)
                                </option>
                                <option value="Asia/Riyadh"
                                    {{ ($settings['timezone'] ?? '') == 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)
                                </option>
                                <option value="Asia/Dubai"
                                    {{ ($settings['timezone'] ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>دبي (GMT+4)
                                </option>
                                <option value="Asia/Kuwait"
                                    {{ ($settings['timezone'] ?? '') == 'Asia/Kuwait' ? 'selected' : '' }}>الكويت (GMT+3)
                                </option>
                                <option value="Asia/Amman"
                                    {{ ($settings['timezone'] ?? '') == 'Asia/Amman' ? 'selected' : '' }}>عمّان (GMT+3)
                                </option>
                                <option value="Africa/Cairo"
                                    {{ ($settings['timezone'] ?? '') == 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">العملة
                                الافتراضية</label>
                            <input type="text" name="currency" value="{{ $settings['currency'] ?? 'دينار عراقي' }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- مظهر الخط -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        مظهر الخط
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">التحكم في نوع وحجم الخط عبر جميع صفحات النظام.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">نوع الخط</label>
                            <select name="font_family" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Cairo" {{ ($settings['font_family'] ?? 'Cairo') == 'Cairo' ? 'selected' : '' }}>Cairo (القاهرة)</option>
                                <option value="Tajawal" {{ ($settings['font_family'] ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal (تجوال)</option>
                                <option value="Nunito" {{ ($settings['font_family'] ?? '') == 'Nunito' ? 'selected' : '' }}>Nunito</option>
                                <option value="Almarai" {{ ($settings['font_family'] ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai (المراعي)</option>
                                <option value="Amiri" {{ ($settings['font_family'] ?? '') == 'Amiri' ? 'selected' : '' }}>Amiri (أميري)</option>
                                <option value="IBM Plex Sans Arabic" {{ ($settings['font_family'] ?? '') == 'IBM Plex Sans Arabic' ? 'selected' : '' }}>IBM Plex Sans Arabic</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">حجم الخط</label>
                            <select name="font_size" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="12" {{ ($settings['font_size'] ?? '14') == '12' ? 'selected' : '' }}>12px (صغير)</option>
                                <option value="13" {{ ($settings['font_size'] ?? '') == '13' ? 'selected' : '' }}>13px</option>
                                <option value="14" {{ ($settings['font_size'] ?? '14') == '14' ? 'selected' : '' }}>14px (افتراضي)</option>
                                <option value="15" {{ ($settings['font_size'] ?? '') == '15' ? 'selected' : '' }}>15px</option>
                                <option value="16" {{ ($settings['font_size'] ?? '') == '16' ? 'selected' : '' }}>16px (كبير)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        إعدادات الأمان
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="checkbox" name="force_https" id="force_https"
                                {{ ($settings['force_https'] ?? '0') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="mr-3">
                                <label for="force_https" class="text-sm font-medium text-gray-900 dark:text-white">فرض
                                    HTTPS</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">إجبار جميع الاتصالات على استخدام
                                    بروتوكول آمن</p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    ⚠️ <strong>ملاحظة:</strong> يتطلب شهادة SSL على الخادم (متاح تلقائياً في معظم
                                    الاستضافات)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="checkbox" name="enable_2fa" id="enable_2fa"
                                {{ ($settings['enable_2fa'] ?? '0') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-1">
                            <div class="mr-3">
                                <label for="enable_2fa" class="text-sm font-medium text-gray-900 dark:text-white">تفعيل
                                    التحقق بخطوتين (2FA)</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">طبقة حماية إضافية عند تسجيل الدخول</p>
                                <div
                                    class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <p class="text-xs text-yellow-700 dark:text-yellow-400 font-medium mb-1">⚠️ ملاحظات
                                        للمراجعة مع الشركة:</p>
                                    <ul class="text-xs text-yellow-600 dark:text-yellow-500 space-y-1 list-disc mr-4">
                                        <li><strong>غير مبرمج حالياً</strong> - يحتاج تطوير إضافي</li>
                                        <li><strong>الخيارات المتاحة:</strong>
                                            <br>• عبر الإيميل (مجاني - يحتاج إعداد SMTP)
                                            <br>• عبر تطبيق Google Authenticator (مجاني)
                                            <br>• عبر SMS (مدفوع - Twilio أو مشابه)
                                        </li>
                                        <li><strong>المتطلبات:</strong> إضافة حقول في جدول المستخدمين + صفحة إدخال الرمز
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">مدة بقاء المستخدم
                                داخل النظام</label>
                            <div class="flex items-center gap-3">
                                <input type="number" name="session_lifetime"
                                    value="{{ $settings['session_lifetime'] ?? '120' }}" min="5" max="1440"
                                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2.5 dark:bg-gray-800 dark:border-gray-500 dark:text-white">
                                <span class="text-sm text-gray-600 dark:text-gray-400">دقيقة</span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                ⏱️ إذا لم يستخدم المستخدم النظام لهذه المدة، سيتم تسجيل خروجه تلقائياً.
                                <br>
                                <span class="text-gray-400">مثال: 120 دقيقة = ساعتين | 60 دقيقة = ساعة واحدة</span>
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info">
                    <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ الإعدادات
                </button>
            </form>

                <!-- معلومات الشركة المالكة (SA) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a6 6 0 016 6v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a6 6 0 016-6zm0 0a6 6 0 00-6 6m6-6a6 6 0 016 6" />
                        </svg>
                        معلومات الشركة المالكة (السوبر أدمن)
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        تعديل بيانات الشركة المالكة من جدول <span class="font-mono">companies</span> (الكود: <span class="font-semibold">SA</span>) مثل الرقم واللوكو والعنوان.
                    </p>

                    <form action="{{ route('admin.settings.owner-company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">اسم الشركة</label>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $ownerCompany->name ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">اسم مدير الشركة</label>
                                <input type="text" name="owner_managername" value="{{ old('owner_managername', $ownerCompany->managername ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">رقم الهاتف</label>
                                <input type="text" name="owner_phone" value="{{ old('owner_phone', $ownerCompany->phone ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="077xxxxxxxx أو 9647xxxxxxxxx">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">سيُستخدم أيضاً في زر واتساب داخل الصفحة التعريفية.</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">البريد الإلكتروني</label>
                                <input type="email" name="owner_email" value="{{ old('owner_email', $ownerCompany->email ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="info@concreteerp.app">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">العنوان</label>
                                <input type="text" name="owner_address" value="{{ old('owner_address', $ownerCompany->address ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ملاحظات</label>
                                <textarea name="owner_note" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('owner_note', $ownerCompany->note ?? '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">شعار الشركة (Logo)</label>
                                <input type="file" name="owner_logo" accept="image/*"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @if (!empty($ownerCompany->logo))
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="{{ asset($ownerCompany->logo) }}" alt="Owner Logo" class="h-14 w-14 rounded-lg object-contain bg-white">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            اللوكو الحالي محفوظ في: <span class="font-mono">{{ $ownerCompany->logo }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-info">
                                <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                حفظ معلومات الشركة المالكة
                            </button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
@endsection
