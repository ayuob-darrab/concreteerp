@extends('layouts.app')

@section('page-title', 'إضافة حساب شركات')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="panel">
            <div class="mb-5">
                <h5 class="text-lg font-semibold dark:text-white-light">إضافة حساب شركة جديد</h5>
                <p class="text-white-dark mt-1">أدخل بيانات الحساب الجديد</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger mb-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mb-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {!! Form::open([
                'route' => 'companies.store',
                'method' => 'POST',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-5">
                <!-- اسم الحساب -->
                <div>
                    <label for="Name" class="block text-sm font-medium mb-2">اسم الحساب الكامل</label>
                    <input id="Name" type="text" required name="fullname" placeholder="أدخل الاسم الكامل"
                        class="form-input" value="{{ old('fullname') }}">
                </div>

                <!-- اسم المستخدم -->
                <div>
                    <label for="Username" class="block text-sm font-medium mb-2">اسم المستخدم</label>
                    <input id="Username" type="text" required name="username" placeholder="أدخل اسم المستخدم"
                        class="form-input" value="{{ old('username') }}" pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label for="Password" class="block text-sm font-medium mb-2">كلمة المرور</label>
                    <input id="Password" type="text" required name="password" placeholder="أدخل كلمة المرور"
                        class="form-input">
                </div>

                <!-- اختيار الشركة -->
                <div>
                    <label for="company_code" class="block text-sm font-medium mb-2">اختيار الشركة</label>
                    <select id="company_code" required name="company_code" class="form-select">
                        <option value="" disabled selected>اختر الشركة</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->code }}"
                                {{ old('company_code') == $company->code ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <div id="subscription-limit-msg" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hidden">
                        <span id="subscription-limit-text"></span>
                    </div>
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary">
                    رجوع
                </button>
                <button type="submit" name="active" value="AddNewTocompany" class="btn btn-primary">
                    إضافة الحساب
                </button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var limits = @json($subscriptionLimits ?? []);
            var select = document.getElementById('company_code');
            var msgBox = document.getElementById('subscription-limit-msg');
            var msgText = document.getElementById('subscription-limit-text');

            function updateLimitMessage() {
                var code = select.value;
                if (!code) {
                    msgBox.classList.add('hidden');
                    return;
                }
                var info = limits[code];
                if (!info) {
                    msgBox.classList.add('hidden');
                    return;
                }
                msgBox.classList.remove('hidden');
                if (info.unlimited) {
                    msgText.textContent = 'مسموح بعدد غير محدود من الحسابات (خطة لا تعتمد على عدد المستخدمين).';
                } else {
                    msgText.textContent = 'المتبقي من الحسابات المسموح بها: ' + info.remaining + ' من ' + info.max + ' (مستخدم حالياً: ' + info.used + ')';
                }
            }

            select.addEventListener('change', updateLimitMessage);
            updateLimitMessage();
        });
    </script>
@endsection
