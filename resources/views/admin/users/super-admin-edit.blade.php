@extends('layouts.app')

@section('page-title', 'تعديل حساب سوبر أدمن')

@section('content')
    <div class="p-4 lg:mt-1.5">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">تعديل حساب سوبر أدمن</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->fullname }} (ID: {{ $user->id }})</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <form action="{{ route('admin.super-admin-users.update', $user->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم الكامل</label>
                            <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}"
                                class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                required>
                            @error('fullname')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم
                                    المستخدم</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required>
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البريد
                                    الإلكتروني</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                كلمة المرور الجديدة (اختياري)
                            </label>
                            <input type="text" name="password"
                                class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                autocomplete="off">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">اترك الحقل فارغاً إذا كنت لا ترغب في تغيير كلمة المرور.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <input id="is_active" type="checkbox" name="is_active" value="1"
                                class="rounded border-gray-300 text-primary focus:ring-primary"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">حساب
                                مفعّل</label>
                        </div>

                        <div class="flex items-center gap-2 pt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.super-admin-users') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                رجوع لقائمة السوبر أدمن
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-2">ملاحظات</h2>
                    <p>هذا الحساب يمتلك صلاحيات <strong>سوبر أدمن</strong> كاملة على النظام.</p>
                    <p>حتى لو تم تغيير نوع المستخدم من مكان آخر، سيتم تثبيته كسوبر أدمن عند حفظ هذا النموذج.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

