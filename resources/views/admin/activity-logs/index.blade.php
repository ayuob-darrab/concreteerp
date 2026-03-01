@extends('layouts.app')

@section('page-title', 'سجلات النشاط')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">سجلات النشاط</h1>
                <p class="text-gray-500 dark:text-gray-400">آخر 100 نشاط في النظام</p>
            </div>

            <!-- Activity Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">المستخدم</th>
                                <th scope="col" class="px-4 py-3">البريد الإلكتروني</th>
                                <th scope="col" class="px-4 py-3">نوع الحساب</th>
                                <th scope="col" class="px-4 py-3">الشركة</th>
                                <th scope="col" class="px-4 py-3">آخر نشاط</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogins as $index => $log)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $log->fullname }}
                                    </td>
                                    <td class="px-4 py-3">{{ $log->email }}</td>
                                    <td class="px-4 py-3">
                                        @if ($log->usertype_id == 'SA')
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">سوبر
                                                أدمن</span>
                                        @elseif($log->usertype_id == 'CM')
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">مدير
                                                شركة</span>
                                        @elseif($log->usertype_id == 'BM')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">مدير
                                                فرع</span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $log->usertype_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $log->company_code }}</td>
                                    <td class="px-4 py-3">{{ $log->updated_at ? $log->updated_at->diffForHumans() : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">لا يوجد سجلات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
