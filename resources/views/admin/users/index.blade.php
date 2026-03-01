@extends('layouts.app')

@section('page-title', 'إدارة المستخدمين')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إدارة المستخدمين</h1>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ number_format($stats['total']) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">إجمالي المستخدمين</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-300">{{ number_format($stats['active']) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">نشط</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-300">{{ number_format($stats['inactive']) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">غير نشط</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">
                        {{ number_format($stats['companies']) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">مدراء شركات</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">
                        {{ number_format($stats['branches']) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">مدراء فروع</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">
                        {{ number_format($stats['contractors']) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">مقاولين</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <!-- فلتر الحالة -->
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                        <select name="status"
                            class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">الكل</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>مفعّل</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>معطّل</option>
                        </select>
                    </div>

                    <!-- فلتر الشركة -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الشركة</label>
                        <select name="company"
                            class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">جميع الشركات</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->code }}"
                                    {{ request('company') == $company->code ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- فلتر نوع الحساب -->
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع الحساب</label>
                        <select name="type"
                            class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">الكل</option>
                            <option value="SA" {{ request('type') == 'SA' ? 'selected' : '' }}>سوبر أدمن</option>
                            <option value="CM" {{ request('type') == 'CM' ? 'selected' : '' }}>مدير شركة</option>
                            <option value="BM" {{ request('type') == 'BM' ? 'selected' : '' }}>مدير فرع</option>
                        </select>
                    </div>

                    <!-- أزرار -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            بحث
                        </button>
                        <a href="{{ route('admin.users') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">الاسم</th>
                                <th scope="col" class="px-4 py-3">البريد الإلكتروني</th>
                                <th scope="col" class="px-4 py-3">نوع الحساب</th>
                                <th scope="col" class="px-4 py-3">الشركة</th>
                                <th scope="col" class="px-4 py-3">الحالة</th>
                                <th scope="col" class="px-4 py-3">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3">{{ $users->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $user->fullname }}
                                    </td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @if ($user->usertype_id == 'SA')
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">سوبر
                                                أدمن</span>
                                        @elseif($user->usertype_id == 'CM')
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">مدير
                                                شركة</span>
                                        @elseif($user->usertype_id == 'BM')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">مدير
                                                فرع</span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $user->usertype_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $user->CompanyName->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($user->is_active)
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">نشط</span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">غير
                                                نشط</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا يوجد مستخدمين</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
