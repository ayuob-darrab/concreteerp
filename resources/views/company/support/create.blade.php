@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="panel">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('support.index') }}" class="text-gray-500 hover:text-primary">
                    <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">📝 إنشاء تذكرة جديدة</h2>
                    <p class="text-sm text-gray-500 mt-1">أخبرنا عن مشكلتك وسنساعدك في أقرب وقت</p>
                </div>
            </div>

            {{-- رسائل الخطأ --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- الموضوع --}}
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium mb-2">
                        عنوان التذكرة <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                        class="form-input w-full" placeholder="مثال: مشكلة في تسجيل الدخول" required>
                </div>

                {{-- التصنيف والأولوية --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="category" class="block text-sm font-medium mb-2">
                            التصنيف <span class="text-danger">*</span>
                        </label>
                        <select id="category" name="category" class="form-select w-full" required>
                            @foreach ($categories as $key => $value)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium mb-2">
                            الأولوية <span class="text-danger">*</span>
                        </label>
                        <select id="priority" name="priority" class="form-select w-full" required>
                            @foreach ($priorities as $key => $value)
                                <option value="{{ $key }}"
                                    {{ old('priority', 'medium') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- الوصف --}}
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium mb-2">
                        وصف المشكلة <span class="text-danger">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" class="form-textarea w-full"
                        placeholder="اشرح المشكلة بالتفصيل... ما الذي حدث؟ ما هي الخطوات التي قمت بها؟" required>{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">كلما كان الوصف أوضح، كلما تمكنا من مساعدتك بشكل أسرع</p>
                </div>

                {{-- المرفقات --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">
                        المرفقات (اختياري)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <input type="file" id="attachments" name="attachments[]" multiple class="hidden"
                            accept="image/*,.pdf,.doc,.docx,.txt">
                        <label for="attachments" class="cursor-pointer">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">اضغط لإضافة ملفات أو اسحب وأفلت هنا</p>
                            <p class="text-xs text-gray-400 mt-1">حد أقصى 5 ملفات، 5MB لكل ملف</p>
                        </label>
                    </div>
                    <div id="filesList" class="mt-3 space-y-2"></div>
                </div>

                {{-- نصائح --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">💡 نصائح للحصول على دعم أسرع:</h4>
                    <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                        <li>• اختر التصنيف المناسب للمشكلة</li>
                        <li>• اشرح خطوات إعادة إنتاج المشكلة إن أمكن</li>
                        <li>• أرفق صور للشاشة (Screenshots) توضح المشكلة</li>
                        <li>• اذكر أي رسالة خطأ ظهرت لك</li>
                    </ul>
                </div>

                {{-- الأزرار --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        إرسال التذكرة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('attachments').addEventListener('change', function(e) {
            const filesList = document.getElementById('filesList');
            filesList.innerHTML = '';

            for (const file of e.target.files) {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded p-2';
                div.innerHTML = `
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <span class="text-sm flex-1 truncate">${file.name}</span>
                    <span class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</span>
                `;
                filesList.appendChild(div);
            }
        });
    </script>
@endsection
