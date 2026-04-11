@extends('layouts.app')

@section('page-title', 'إدارة المستخدمين')

@section('content')
    <div class="p-4 lg:mt-1.5">
        <div class="w-full">
            {{-- لوحة واحدة: عنوان + إحصائيات + فلاتر في نفس المساحة (عمودان على الشاشات العريضة) --}}
            <div
                class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-5">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إدارة المستخدمين</h1>

                <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-12 xl:items-end xl:gap-5">
                    {{-- إحصائيات مدمجة بشكل مضغوط --}}
                    <div class="xl:col-span-7 2xl:col-span-8">
                        <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">ملخص سريع</p>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
                            <div class="rounded-lg bg-blue-50 p-2.5 dark:bg-blue-900/40 sm:p-3">
                                <div class="text-lg font-bold text-blue-600 sm:text-xl dark:text-blue-300">
                                    {{ number_format($stats['total']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">إجمالي المستخدمين</div>
                            </div>
                            <div class="rounded-lg bg-green-50 p-2.5 dark:bg-green-900/40 sm:p-3">
                                <div class="text-lg font-bold text-green-600 sm:text-xl dark:text-green-300">
                                    {{ number_format($stats['active']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">نشط</div>
                            </div>
                            <div class="rounded-lg bg-red-50 p-2.5 dark:bg-red-900/40 sm:p-3">
                                <div class="text-lg font-bold text-red-600 sm:text-xl dark:text-red-300">
                                    {{ number_format($stats['inactive']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">غير نشط</div>
                            </div>
                            <div class="rounded-lg bg-purple-50 p-2.5 dark:bg-purple-900/40 sm:p-3">
                                <div class="text-lg font-bold text-purple-600 sm:text-xl dark:text-purple-300">
                                    {{ number_format($stats['companies']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">مدراء شركات</div>
                            </div>
                            <div class="rounded-lg bg-yellow-50 p-2.5 dark:bg-yellow-900/40 sm:p-3">
                                <div class="text-lg font-bold text-yellow-600 sm:text-xl dark:text-yellow-300">
                                    {{ number_format($stats['branches']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">مدراء فروع</div>
                            </div>
                            <div class="rounded-lg bg-gray-100 p-2.5 dark:bg-gray-700/80 sm:p-3">
                                <div class="text-lg font-bold text-gray-700 sm:text-xl dark:text-gray-200">
                                    {{ number_format($stats['contractors']) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">مقاولين</div>
                            </div>
                        </div>
                    </div>

                    {{-- فلاتر بجانب الإحصائيات على الشاشات الكبيرة --}}
                    <div class="xl:col-span-5 2xl:col-span-4">
                        <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">تصفية القائمة</p>
                        <form action="{{ route('admin.users') }}" method="GET"
                            class="flex flex-wrap items-end gap-2">
                            <div class="min-w-0 flex-1 sm:min-w-[8rem]">
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">الحالة</label>
                                <select name="status"
                                    class="form-select w-full rounded-lg border-gray-300 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">الكل</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>مفعّل</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>معطّل</option>
                                </select>
                            </div>
                            <div class="min-w-0 flex-1 sm:min-w-[10rem]">
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">الشركة</label>
                                <select name="company"
                                    class="form-select w-full rounded-lg border-gray-300 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">جميع الشركات</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->code }}"
                                            {{ request('company') == $company->code ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="min-w-0 flex-1 sm:min-w-[8rem]">
                                <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">نوع الحساب</label>
                                <select name="type"
                                    class="form-select w-full rounded-lg border-gray-300 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">الكل</option>
                                    <option value="CM" {{ request('type') == 'CM' ? 'selected' : '' }}>مدير شركة</option>
                                    <option value="BM" {{ request('type') == 'BM' ? 'selected' : '' }}>مدير فرع</option>
                                </select>
                            </div>
                            <div class="flex gap-2 sm:shrink-0">
                                <button type="submit"
                                    class="btn btn-primary btn-sm whitespace-nowrap px-4 py-2">
                                    <svg class="h-3.5 w-3.5 inline-block ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    بحث
                                </button>
                                <a href="{{ route('admin.users') }}"
                                    class="btn btn-outline-secondary btn-sm whitespace-nowrap px-4 py-2">
                                    إعادة تعيين
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
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
                                                class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">غير
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
