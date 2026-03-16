@extends('layouts.app')

@section('page-title', 'حسابات السوبر أدمن')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">حسابات السوبر أدمن</h1>
                <a href="{{ route('admin.super-admin-users.create') }}"
                    class="btn btn-primary inline-flex items-center gap-2"
                    style="background-color:#16a34a !important; border-color:#15803d !important; color:#fff !important;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة حساب سوبر أدمن جديد
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">
                        {{ number_format($stats['total'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">إجمالي حسابات السوبر أدمن</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-300">
                        {{ number_format($stats['active'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">حسابات نشطة</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-300">
                        {{ number_format($stats['inactive'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">حسابات غير نشطة</div>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                <form action="{{ route('admin.super-admin-users') }}" method="GET" class="flex flex-wrap items-end gap-4">
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

                    <!-- أزرار البحث -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            بحث
                        </button>
                        <a href="{{ route('admin.super-admin-users') }}"
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
                                <th scope="col" class="px-4 py-3">اسم المستخدم</th>
                                <th scope="col" class="px-4 py-3">البريد الإلكتروني</th>
                                <th scope="col" class="px-4 py-3">نوع الحساب</th>
                                <th scope="col" class="px-4 py-3">الشركة</th>
                                <th scope="col" class="px-4 py-3">الحالة</th>
                                <th scope="col" class="px-4 py-3">تاريخ التسجيل</th>
                                <th scope="col" class="px-4 py-3 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3">{{ $users->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $user->fullname }}</td>
                                    <td class="px-4 py-3">{{ $user->username ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @if($user->usertype_id === 'SA')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">سوبر أدمن</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">أدمن</span>
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
                                        {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.super-admin-users.edit', $user->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                                            تعديل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">لا يوجد حسابات</td>
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

