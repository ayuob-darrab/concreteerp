@extends('layouts.app')

@section('page-title', 'الموافقة على السلف')

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">

        {{-- إشعارات السلف الجديدة --}}
        @if (isset($advanceNotifications) && $advanceNotifications->count() > 0)
            <div class="panel bg-warning-light dark:bg-warning-dark-light">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-warning text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                        </span>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-warning">
                            🔔 لديك {{ $advanceNotifications->count() }} طلب سلفة جديد بانتظار الموافقة
                        </h4>
                        <div class="mt-2 space-y-1">
                            @foreach ($advanceNotifications as $notification)
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    • {{ $notification->message }}
                                    <span
                                        class="text-xs text-gray-400">({{ $notification->created_at->diffForHumans() }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <i class="fas fa-clock text-warning ml-2"></i>
                    السلف المعلقة - بانتظار الموافقة
                    @if (isset($advanceNotifications) && $advanceNotifications->count() > 0)
                        <span class="badge bg-warning text-dark mr-2">{{ $advanceNotifications->count() }} جديد</span>
                    @endif
                </h5>
                <div class="flex gap-2">
                    <a href="{{ route('advances.index') }}" class="btn btn-outline-secondary flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة</span>
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-5">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-5">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($advances->count() > 0)
                <div class="table-responsive">
                    <table class="table-striped">
                        <thead>
                            <tr class="bg-warning/20">
                                <th>#</th>
                                <th>رقم السلفة</th>
                                <th>المستفيد</th>
                                <th>نوع المستفيد</th>
                                <th>المبلغ</th>
                                <th>تاريخ الطلب</th>
                                <th>طالب السلفة</th>
                                <th>السبب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advances as $advance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('advances.show', $advance) }}"
                                            class="text-primary font-semibold hover:underline">
                                            {{ $advance->advance_number }}
                                        </a>
                                    </td>
                                    <td>{{ $advance->beneficiary_name }}</td>
                                    <td>
                                        @switch($advance->beneficiary_type)
                                            @case('employee')
                                                <span class="badge bg-info">موظف</span>
                                            @break

                                            @case('contractor')
                                                <span class="badge bg-primary">مقاول</span>
                                            @break

                                            @case('supplier')
                                                <span class="badge bg-warning">مورد</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="font-bold text-primary">{{ number_format($advance->amount) }} د.ع</td>
                                    <td>{{ $advance->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $advance->requester->name ?? '-' }}</td>
                                    <td>{{ Str::limit($advance->reason, 30) ?? '-' }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('advances.show', $advance) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                    <h4 class="text-lg font-semibold mb-2">لا توجد سلف معلقة</h4>
                    <p class="text-gray-500">جميع طلبات السلف تمت معالجتها</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal الرفض -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-danger text-white p-4 rounded-t-lg">
                    <h5 class="text-lg font-semibold">رفض السلفة</h5>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">سبب الرفض <span class="text-danger">*</span></span>
                        </label>
                        <textarea name="reason" class="form-input" rows="3" required placeholder="يرجى ذكر سبب رفض السلفة"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeRejectModal()">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض السلفة</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // فتح صفحة الطباعة في نافذة جديدة إذا تمت الموافقة
        @if (session('print_url'))
            window.open('{{ session('print_url') }}', '_blank');
        @endif

        function openRejectModal(advanceId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = '/ConcreteERP/advances/' + advanceId + '/reject';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // إغلاق المودال عند الضغط خارجه
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
@endpush
