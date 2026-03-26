@extends('layouts.app')

@section('page-title', 'تعديل: ' . ($pageLabels[$pageKey] ?? $pageKey))

@section('content')
    @php
        $fClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400';
        $lblClass = 'block mb-2 text-sm font-medium text-gray-900 dark:text-white';
    @endphp

    <div class="p-4 bg-white block border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 max-w-5xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $pageLabels[$pageKey] ?? $pageKey }}</h1>
            <a href="{{ route('admin.display-pages.index') }}" class="text-sm text-primary hover:underline">← كل الصفحات</a>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400">{{ session('success') }}</div>
        @endif

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 max-w-2xl">
            عنوان ونص تمهيدي للصفحة العامة، ثم حقول ثابتة لكل قناة. يظهر زر القناة في الموقع فقط إذا وُجدت قيمة في حقلها.
        </p>

        <form action="{{ route('admin.display-pages.contact.update') }}" method="post" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-600 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">المحتوى</h2>
                <div class="space-y-4">
                    <div>
                        <label class="{{ $lblClass }}">عنوان الصفحة (يظهر بدل «تواصل معنا» إن وُجد)</label>
                        <input type="text" name="title" value="{{ old('title', $contactSettings->title) }}"
                            class="{{ $fClass }}"
                            placeholder="مثال: تواصل مع فريق ConcreteERP">
                    </div>
                    <div>
                        <label class="{{ $lblClass }}">النص التمهيدي</label>
                        <textarea name="intro_text" rows="5" class="{{ $fClass }}"
                            placeholder="فقرة ترحيبية أمام أزرار التواصل">{{ old('intro_text', $contactSettings->intro_text) }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $lblClass }}">نص تلميح أسفل القنوات (اختياري)</label>
                        <textarea name="hint_text" rows="3" class="{{ $fClass }}"
                            placeholder="مثال: اذكر اسم الشركة عند المراسلة">{{ old('hint_text', $contactSettings->hint_text) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-600 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">وسائل التواصل (ثابتة)</h2>

                @php
                    $fields = [
                        ['name' => 'email', 'label' => 'البريد الإلكتروني', 'fa' => 'fas fa-envelope', 'placeholder' => 'info@example.com'],
                        ['name' => 'whatsapp', 'label' => 'واتساب', 'fa' => 'fab fa-whatsapp', 'placeholder' => '9647XXXXXXXX (بدون +)'],
                        ['name' => 'telegram', 'label' => 'تيليجرام', 'fa' => 'fab fa-telegram', 'placeholder' => 'اسم المستخدم أو رابط t.me/...'],
                        ['name' => 'facebook', 'label' => 'فيسبوك', 'fa' => 'fab fa-facebook-f', 'placeholder' => 'رابط الصفحة أو اسم المستخدم'],
                        ['name' => 'instagram', 'label' => 'إنستغرام', 'fa' => 'fab fa-instagram', 'placeholder' => 'اسم الحساب أو الرابط'],
                        ['name' => 'phone', 'label' => 'هاتف', 'fa' => 'fas fa-phone', 'placeholder' => 'رقم للاتصال'],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach ($fields as $f)
                        <div class="flex gap-3 items-start">
                            <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-primary dark:bg-gray-700 dark:text-white">
                                <i class="{{ $f['fa'] }} text-lg" aria-hidden="true"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <label class="{{ $lblClass }}">{{ $f['label'] }}</label>
                                <input type="text" name="{{ $f['name'] }}"
                                    value="{{ old($f['name'], $contactSettings->{$f['name']}) }}"
                                    class="{{ $fClass }}"
                                    placeholder="{{ $f['placeholder'] }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-lg text-sm font-medium">حفظ</button>
        </form>
    </div>
@endsection
