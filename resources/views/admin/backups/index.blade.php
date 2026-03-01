@extends('layouts.app')

@section('page-title', 'النسخ الاحتياطي')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">سجل النسخ الاحتياطي</h1>
                        @if ($lastBackupDate)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                آخر نسخة: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $lastBackupDate }}</span>
                            </p>
                        @endif
                    </div>
                    <div class="w-full mt-2">
                            <form action="{{ route('admin.backups.create') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    <span>نسخة كاملة (قاعدة بيانات + ملفات)</span>
                                </button>
                            </form>
                        </div>
                </div>
                @if (!($zipAvailable ?? true))
                    <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 text-sm space-y-1">
                        <p>امتداد Zip غير مفعّل. لتفعيله: Laragon → PHP → php.ini → ابحث عن <code class="bg-amber-100 dark:bg-amber-800 px-1 rounded">extension=zip</code> وأزل التعليق (; من بداية السطر).</p>
                        <p class="font-medium">بعد تفعيل <code>extension=zip</code> يجب <strong>إعادة تشغيل Laragon بالكامل</strong> (Stop All ثم Start) حتى يُحمّل التعديل.</p>
                        <p class="mt-2 pt-2 border-t border-amber-200 dark:border-amber-700"><strong>نسخ مجلد المرفقات يدوياً:</strong> انسخ المجلد من المسار: <code class="bg-amber-100 dark:bg-amber-800 px-1 rounded break-all">{{ $uploadsPath ?? public_path('uploads') }}</code></p>
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">بعد أي تعديل على php.ini (مثل <code>extension=zip</code>) يجب <strong>إعادة تشغيل Laragon بالكامل</strong> (Stop All ثم Start) حتى يُحمّل التعديل.</p>
                @endif
            </div>

            @if (session('success'))
                <div
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {!! nl2br(e(session('success'))) !!}
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

            <!-- ما يتضمنه النسخ الاحتياطي -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 flex items-start gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">قاعدة البيانات</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">جميع جداول قاعدة البيانات والبيانات</p>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 dark:text-white">مجلد uploads</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">شعارات الشركات وملفات الموظفين</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1 break-all">{{ $uploadsPath ?? public_path('uploads') }}</p>
                    </div>
                </div>
            </div>

            <!-- Backups History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">سجل عمليات النسخ الاحتياطي</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">النسخ يتم تنزيلها مباشرة على جهازك ولا تُحفظ على
                        الخادم</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">اسم الملف</th>
                                <th scope="col" class="px-4 py-3">النوع</th>
                                <th scope="col" class="px-4 py-3">الحجم</th>
                                <th scope="col" class="px-4 py-3">التاريخ</th>
                                <th scope="col" class="px-4 py-3">بواسطة</th>
                                <th scope="col" class="px-4 py-3">الشركات</th>
                                <th scope="col" class="px-4 py-3">المستخدمين</th>
                                <th scope="col" class="px-4 py-3">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $index => $backup)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-5L9 4H4zm7 5a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white text-xs">{{ $backup->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if (str_contains($backup->notes ?? '', 'تلقائي'))
                                            <span
                                                class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                                <svg class="w-3 h-3 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                تلقائي
                                            </span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                                يدوي
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $backup->size }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="text-gray-700 dark:text-gray-300">{{ $backup->creator->fullname ?? 'غير معروف' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                                            {{ $backup->companies_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $backup->users_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $backup->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                            <p class="text-lg font-medium">لا يوجد سجل نسخ احتياطي</p>
                                            <p class="text-sm text-gray-400 mt-1">قم بإنشاء أول نسخة احتياطية</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Backup Info -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">معلومات هامة</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1 list-disc mr-4">
                            <li>النسخة تُنزّل مباشرة على جهازك ولا تُحفظ على الخادم (لتوفير المساحة)</li>
                            <li>يُنصح بإنشاء نسخة احتياطية بشكل دوري (يومياً أو أسبوعياً)</li>
                            <li>احفظ النسخ في مكان آمن (Google Drive, Dropbox, قرص خارجي)</li>
                            <li>النسخة تحتوي على قاعدة البيانات + مجلد uploads (شعارات وملفات)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
