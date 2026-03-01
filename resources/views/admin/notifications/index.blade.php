@extends('layouts.app')

@section('page-title', 'إرسال إشعار')

@section('content')
    <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 lg:mt-1.5">
        <div class="w-full max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white flex items-center gap-2">
                        <span class="text-2xl">📢</span>
                        إرسال إشعار جديد
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">اختر المستلمين، اكتب العنوان والنص، ثم أرسل الإشعار</p>
                </div>
                <a href="{{ route('admin.notifications.list') }}"
                    class="btn btn-secondary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    عرض قائمة الإشعارات
                </a>
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300">
                    <p class="font-medium mb-2">يرجى تصحيح الأخطاء التالية:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('admin.notifications.send') }}" method="POST" id="notificationForm">
                    @csrf

                    {{-- Quick templates --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">قوالب سريعة (تعبئة النموذج)</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="fillTemplate('maintenance')"
                                class="px-3 py-2 text-sm rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 hover:bg-amber-200 dark:hover:bg-amber-800/40 transition-colors">
                                🔧 صيانة
                            </button>
                            <button type="button" onclick="fillTemplate('update')"
                                class="px-3 py-2 text-sm rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800/40 transition-colors">
                                🆕 تحديث
                            </button>
                            <button type="button" onclick="fillTemplate('reminder')"
                                class="px-3 py-2 text-sm rounded-lg bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800/40 transition-colors">
                                ⏰ تذكير
                            </button>
                            <button type="button" onclick="fillTemplate('announcement')"
                                class="px-3 py-2 text-sm rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-800/40 transition-colors">
                                📢 إعلان
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Row: المستلمين + نوع الإشعار --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="company_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    المستلمين
                                </label>
                                <select name="company_code" id="company_code" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="all">📢 جميع الشركات</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->code }}">🏢 {{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نوع الإشعار
                                </label>
                                <select name="type" id="type" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="info">ℹ️ معلومات</option>
                                    <option value="warning">⚠️ تحذير</option>
                                    <option value="success">✅ نجاح</option>
                                    <option value="danger">🔴 طارئ</option>
                                </select>
                            </div>
                        </div>

                        {{-- عنوان الإشعار --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                عنوان الإشعار
                            </label>
                            <input type="text" name="title" id="title" required maxlength="255"
                                placeholder="مثال: صيانة دورية للنظام"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                        </div>

                        {{-- نص الإشعار --}}
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                نص الإشعار
                            </label>
                            <textarea name="message" id="message" required rows="4"
                                placeholder="اكتب محتوى الإشعار هنا..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 resize-none"></textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                class="btn btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                إرسال الإشعار
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fillTemplate(type) {
            var templates = {
                maintenance: {
                    title: 'صيانة مجدولة للنظام',
                    message: 'سيتم إجراء صيانة دورية للنظام. قد يكون النظام غير متاح لفترة قصيرة. نعتذر عن أي إزعاج.',
                    type: 'warning'
                },
                update: {
                    title: 'تحديث جديد متاح',
                    message: 'تم إصدار تحديث جديد يتضمن تحسينات وميزات جديدة. يرجى تحديث النظام للحصول على أفضل تجربة.',
                    type: 'success'
                },
                reminder: {
                    title: 'تذكير بتجديد الاشتراك',
                    message: 'نود تذكيركم بأن اشتراككم سينتهي قريباً. يرجى تجديد الاشتراك لتجنب انقطاع الخدمة.',
                    type: 'warning'
                },
                announcement: {
                    title: 'إعلان هام',
                    message: 'لدينا إعلان مهم نود مشاركته معكم. يرجى قراءة التفاصيل بعناية.',
                    type: 'info'
                }
            };
            var t = templates[type];
            if (t) {
                document.getElementById('title').value = t.title;
                document.getElementById('message').value = t.message;
                document.getElementById('type').value = t.type;
                document.getElementById('company_code').value = 'all';
                document.getElementById('notificationForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
@endsection
