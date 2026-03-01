@extends('layouts.app')

@section('page-title', 'سجل الأخطاء')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">سجل الأخطاء</h1>
                <form action="{{ route('admin.error-logs.clear') }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من مسح السجل؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="font-medium rounded-lg text-sm px-5 py-2.5 focus:ring-4 focus:ring-red-300 focus:outline-none dark:focus:ring-red-800 hover:opacity-90 transition-opacity"
                        style="background-color:#b91c1c;color:#fff;">
                        مسح السجل
                    </button>
                </form>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Logs -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">آخر 100 سطر من السجل</h3>
                </div>
                <div class="p-4 max-h-[600px] overflow-y-auto">
                    @if (count($logs) > 0)
                        <pre
                            class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-wrap font-mono bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
@foreach ($logs as $line)
{{ $line }}
@endforeach
</pre>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500">السجل فارغ - لا يوجد أخطاء</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Log Info -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">معلومات</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">مسار ملف السجل: storage/logs/laravel.log</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
