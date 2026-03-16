@extends('layouts.app')

@section('page-title', 'تحديث حساب الشركات')

@section('content')
    <style>
        .stats-card {
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background-color: #10b981;
            color: white;
        }

        .status-inactive {
            background-color: #f59e0b;
            color: white;
        }
    </style>

    <div x-data="companiesTable()">
        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
            <div class="stats-card bg-white dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-400">إجمالي الحسابات</div>
                <div class="text-2xl font-bold mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>

            <div class="stats-card bg-white dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-400">الحسابات النشطة</div>
                <div class="text-2xl font-bold mt-1 text-green-600">{{ $stats['active'] ?? 0 }}</div>
            </div>

            <div class="stats-card bg-white dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-400">الحسابات المعطلة</div>
                <div class="text-2xl font-bold mt-1 text-orange-600">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>

        <!-- الجدول -->
        <div class="panel mt-6">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">حسابات الشركات</h5>
                <a href="{{ url('companies/NewAccountsCompany') }}" class="btn btn-primary">
                    إضافة حساب شركة جديد
                </a>
            </div>

            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th>الاسم الثلاثي</th>
                            <th>اسم المستخدم</th>
                            <th>الشركة</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $user->fullname }}</div>
                                </td>
                                <td>{{ $user->username }}</td>
                                <td>
                                    @if ($user->CompanyName)
                                        <div class="flex items-center gap-2">
                                            @if ($user->CompanyName->logo)
                                                <img src="{{ asset('uploads/' . $user->CompanyName->code . '/companies_logo/' . $user->CompanyName->logo) }}"
                                                    alt="{{ $user->CompanyName->name }}"
                                                    class="w-8 h-8 rounded-full object-cover"
                                                    onerror="this.style.display='none'">
                                            @endif
                                            <span>{{ $user->CompanyName->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="status-badge status-active">مفعل</span>
                                    @else
                                        <span class="status-badge status-inactive">معطل</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ url('companies/' . $user->id . '&editCompanyAccount/edit') }}"
                                            class="btn btn-sm btn-outline-primary" title="تعديل">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z" />
                                            </svg>
                                        </a>
                                        <button type="button"
                                            @click="openPasswordModal({{ $user->id }}, '{{ $user->fullname }}')"
                                            class="btn btn-sm btn-outline-danger" title="إعادة تعيين كلمة المرور">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="11" x="3" y="11" rx="2"
                                                    ry="2" />
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Password Modal -->
        <div x-show="showModal" x-cloak @click.outside="showModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-md shadow-2xl m-4" @click.stop>
                <div class="p-5 border-b dark:border-gray-700">
                    <h5 class="text-lg font-semibold dark:text-white">تغيير كلمة المرور</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">للمستخدم: <span x-text="selectedUserName"
                            class="font-semibold"></span></p>
                </div>
                <form :action="`${baseUrl}/companies/${selectedUserId}`" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="p-5">
                        <label class="block mb-2 text-sm font-medium dark:text-white">كلمة المرور الجديدة</label>
                        <input type="text" name="newPassword" x-model="newPassword" class="form-input w-full"
                            placeholder="أدخل كلمة المرور الجديدة" required>
                    </div>
                    <div class="p-5 border-t dark:border-gray-700 flex gap-2 justify-end">
                        <button type="button" @click="showModal = false" class="btn btn-outline-danger">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('companiesTable', () => ({
                showModal: false,
                selectedUserId: null,
                selectedUserName: '',
                newPassword: '',

                openPasswordModal(userId, userName) {
                    this.selectedUserId = userId;
                    this.selectedUserName = userName;
                    this.newPassword = '';
                    this.showModal = true;
                }
            }));
        });
    </script>
@endsection
