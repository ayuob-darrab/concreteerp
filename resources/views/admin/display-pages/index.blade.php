@extends('layouts.app')

@section('page-title', 'صفحات العرض العامة')

@section('content')
    <div class="p-4 bg-white block border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white mb-2">صفحات العرض</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            تعديل محتوى الصفحات التعريفية للزوار (الرئيسية، فوائد النظام، المميزات، عن النظام، التواصل). الترتيب داخل كل صفحة يحدد ترتيب العرض.
        </p>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400">{{ session('success') }}</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($labels as $key => $label)
                <a href="{{ route('admin.display-pages.edit', $key) }}"
                    class="flex flex-col p-4 border border-gray-200 rounded-lg shadow-sm bg-white dark:bg-gray-800 dark:border-gray-600 hover:border-primary dark:hover:border-primary transition text-gray-900 dark:text-white">
                    <span class="font-semibold">{{ $label }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $key }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
