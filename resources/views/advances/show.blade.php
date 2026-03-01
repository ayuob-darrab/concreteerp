@extends('layouts.app')

@section('page-title', 'تفاصيل السلفة - ' . $advance->advance_number)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <span>سلفة رقم:</span>
                    <span class="text-primary font-bold">{{ $advance->advance_number }}</span>
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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- القسم الأيسر: معلومات السلفة -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- معلومات المستفيد والسلفة -->
                    <div class="panel">
                        <div class="mb-5 flex items-center justify-between">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-info-circle text-primary ml-2"></i>
                                معلومات السلفة
                            </h5>
                        </div>

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- معلومات المستفيد -->
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">رقم السلفة:</span>
                                    <span class="font-bold">{{ $advance->advance_number }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">المستفيد:</span>
                                    <span class="font-semibold">{{ $advance->beneficiary_name }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">نوع المستفيد:</span>
                                    <span>
                                        @switch($advance->beneficiary_type)
                                            @case('employee')
                                                <span class="badge bg-info">موظف</span>
                                            @break

                                            @case('agent')
                                                <span class="badge bg-secondary">مندوب</span>
                                            @break

                                            @case('supplier')
                                                <span class="badge bg-warning">مورد</span>
                                            @break

                                            @case('contractor')
                                                <span class="badge bg-primary">مقاول</span>
                                            @break
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">الفرع:</span>
                                    <span>{{ $advance->branch->name ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- معلومات المبالغ -->
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">مبلغ السلفة:</span>
                                    <span class="font-bold text-primary">{{ number_format($advance->amount) }} د.ع</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">المسدد:</span>
                                    <span class="text-success font-semibold">{{ number_format($advance->paid_amount) }}
                                        د.ع</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">المتبقي:</span>
                                    <span class="text-danger font-semibold">{{ number_format($advance->remaining_amount) }}
                                        د.ع</span>
                                </div>
                                <div class="flex justify-between items-center pb-2">
                                    <span class="text-white-dark">نسبة الإنجاز:</span>
                                    <div class="w-1/2">
                                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                                            <div class="bg-success h-4 rounded-full text-xs text-white text-center leading-4"
                                                style="width: {{ $advance->completion_percentage }}%">
                                                {{ $advance->completion_percentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- معلومات الاستقطاع -->
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">نوع الاستقطاع:</span>
                                    <span>{{ $advance->deduction_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">قيمة الاستقطاع:</span>
                                    <span>{{ number_format($advance->deduction_value) }}{{ $advance->deduction_type == 'percentage' ? '%' : ' د.ع' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">استقطاع تلقائي:</span>
                                    <span>
                                        @if ($advance->auto_deduction)
                                            <span class="badge bg-success">مفعل</span>
                                        @else
                                            <span class="badge bg-secondary">معطل</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- معلومات التواريخ -->
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">تاريخ الطلب:</span>
                                    <span>{{ $advance->requested_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <span class="text-white-dark">طالب السلفة:</span>
                                    <span>{{ $advance->requester->name ?? '-' }}</span>
                                </div>
                                @if ($advance->approved_at)
                                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <span class="text-white-dark">تاريخ الموافقة:</span>
                                        <span>{{ $advance->approved_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($advance->reason)
                            <hr class="my-4 border-gray-200 dark:border-gray-700">
                            <div class="space-y-2">
                                <span class="text-white-dark font-semibold">سبب السلفة:</span>
                                <p class="bg-gray-100 dark:bg-gray-800 p-3 rounded">{{ $advance->reason }}</p>
                            </div>
                        @endif

                        @if ($advance->notes)
                            <div class="space-y-2 mt-4">
                                <span class="text-white-dark font-semibold">ملاحظات:</span>
                                <p class="bg-gray-100 dark:bg-gray-800 p-3 rounded">{{ $advance->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- سجل الدفعات -->
                    <div class="panel">
                        <div class="mb-5 flex items-center justify-between">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-money-bill-wave text-success ml-2"></i>
                                سجل الدفعات
                            </h5>
                        </div>

                        @if ($advance->payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>رقم الدفعة</th>
                                            <th>نوع الدفعة</th>
                                            <th>المبلغ</th>
                                            <th>الرصيد قبل</th>
                                            <th>الرصيد بعد</th>
                                            <th>التاريخ</th>
                                            <th>طباعة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($advance->payments as $payment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $payment->payment_number }}</td>
                                                <td>
                                                    @switch($payment->payment_type)
                                                        @case('manual')
                                                            <span class="badge bg-primary">يدوي</span>
                                                        @break

                                                        @case('salary_deduction')
                                                            <span class="badge bg-info">خصم راتب</span>
                                                        @break

                                                        @case('invoice_deduction')
                                                            <span class="badge bg-warning">خصم فاتورة</span>
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td class="text-success font-semibold">
                                                    {{ number_format($payment->amount) }}</td>
                                                <td>{{ number_format($payment->balance_before) }}</td>
                                                <td>{{ number_format($payment->balance_after) }}</td>
                                                <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                                <td>
                                                    <a href="{{ route('advances.payment.print', $payment) }}"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm flex items-center gap-1"
                                                        title="طباعة الإيصال">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                            <path
                                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                                            </path>
                                                            <rect x="6" y="14" width="12" height="8"></rect>
                                                        </svg>
                                                        <span>طباعة</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th colspan="3" class="text-center">الإجمالي</th>
                                            <th class="text-success">
                                                {{ number_format($advance->payments->sum('amount')) }}
                                                د.ع</th>
                                            <th colspan="4"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-receipt fa-3x text-gray-400 mb-3"></i>
                                <p class="text-gray-500">لا توجد دفعات مسجلة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- القسم الأيمن: الحالة والإجراءات -->
                <div class="space-y-6">

                    <!-- حالة السلفة -->
                    <div class="panel">
                        <div class="mb-5">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-flag ml-2"></i>
                                حالة السلفة
                            </h5>
                        </div>
                        <div class="text-center py-4">
                            @switch($advance->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark text-xl px-6 py-3">معلقة</span>
                                    <p class="text-gray-500 mt-3">بانتظار الموافقة</p>
                                @break

                                @case('approved')
                                    <span class="badge bg-info text-xl px-6 py-3">موافق عليها</span>
                                    <p class="text-gray-500 mt-3">تمت الموافقة - بانتظار التفعيل</p>
                                @break

                                @case('active')
                                    <span class="badge bg-success text-xl px-6 py-3">نشطة</span>
                                    <p class="text-gray-500 mt-3">السلفة فعالة - يمكن التسديد</p>
                                @break

                                @case('completed')
                                    <span class="badge bg-primary text-xl px-6 py-3">مكتملة</span>
                                    <p class="text-gray-500 mt-3">تم سداد السلفة بالكامل</p>
                                @break

                                @case('cancelled')
                                    <span class="badge bg-danger text-xl px-6 py-3">ملغاة</span>
                                    <p class="text-gray-500 mt-3">تم إلغاء السلفة</p>
                                @break
                            @endswitch
                        </div>
                    </div>

                    <!-- الإجراءات -->
                    <div class="panel">
                        <div class="mb-5">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                ⚙️ الإجراءات
                            </h5>
                        </div>
                        <div class="space-y-3">
                            {{-- زر طباعة الفاتورة - يظهر دائماً للسلف الموافق عليها أو النشطة أو المكتملة --}}
                            @if (in_array($advance->status, ['approved', 'active', 'completed']))
                                <a href="{{ route('advances.print', $advance) }}" target="_blank"
                                    class="btn btn-primary w-full flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    <span>🖨️ طباعة الفاتورة</span>
                                </a>
                                <hr class="border-gray-200 dark:border-gray-700">
                            @endif

                            @if ($advance->status == 'pending')
                                <form action="{{ route('advances.approve', $advance) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-full"
                                        onclick="return confirm('هل تريد الموافقة على السلفة بمبلغ {{ number_format($advance->amount) }} د.ع؟')">
                                        ✓ الموافقة على السلفة
                                    </button>
                                </form>

                                <button type="button" class="btn btn-warning w-full" onclick="openEditApproveModal()">
                                    ✏️ موافقة مع تعديل المبلغ
                                </button>

                                <button type="button" class="btn btn-danger w-full" onclick="openRejectModal()">
                                    ✗ رفض السلفة
                                </button>
                            @endif

                            @if ($advance->status == 'active')
                                <form action="{{ route('advances.toggle-auto', $advance) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-{{ $advance->auto_deduction ? 'warning' : 'info' }} w-full flex items-center justify-center gap-2">
                                        <i class="fas fa-sync"></i>
                                        <span>{{ $advance->auto_deduction ? 'تعطيل الاستقطاع التلقائي' : 'تفعيل الاستقطاع التلقائي' }}</span>
                                    </button>
                                </form>

                                <button type="button"
                                    class="btn btn-outline-danger w-full flex items-center justify-center gap-2"
                                    onclick="openCancelModal()">
                                    <i class="fas fa-ban"></i>
                                    <span>إلغاء السلفة</span>
                                </button>
                            @endif

                            @if (in_array($advance->status, ['pending', 'cancelled']))
                                <hr class="border-gray-200 dark:border-gray-700">
                                <form action="{{ route('advances.destroy', $advance) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-outline-danger w-full flex items-center justify-center gap-2"
                                        onclick="return confirm('هل أنت متأكد من حذف هذه السلفة؟')">
                                        <i class="fas fa-trash"></i>
                                        <span>حذف السلفة</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- ملخص -->
                    <div class="panel">
                        <div class="mb-5">
                            <h5 class="text-lg font-semibold dark:text-white-light">
                                <i class="fas fa-chart-pie ml-2"></i>
                                ملخص
                            </h5>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">عدد الدفعات:</span>
                                <span class="font-bold">{{ $advance->payments->count() }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-white-dark">آخر دفعة:</span>
                                <span
                                    class="font-bold">{{ $advance->payments->last()?->paid_at?->format('Y-m-d') ?? '-' }}</span>
                            </div>
                            @if ($advance->status == 'active' && $advance->deduction_type == 'percentage' && $advance->deduction_value > 0)
                                <div class="flex justify-between pb-2">
                                    <span class="text-white-dark">الأقساط المتوقعة:</span>
                                    <span
                                        class="font-bold">{{ ceil($advance->remaining_amount / (($advance->amount * $advance->deduction_value) / 100)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الموافقة مع تعديل المبلغ -->
    <div id="editApproveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <form action="{{ route('advances.approve-with-edit', $advance) }}" method="POST">
                @csrf
                <div class="bg-warning text-dark p-4 rounded-t-lg">
                    <h5 class="text-lg font-semibold">✏️ موافقة مع تعديل المبلغ</h5>
                </div>
                <div class="p-4">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        المبلغ المطلوب: <strong>{{ number_format($advance->amount) }} د.ع</strong>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2">
                                <span class="text-gray-700 dark:text-gray-300">المبلغ الجديد <span
                                        class="text-danger">*</span></span>
                            </label>
                            <div class="flex">
                                <input type="number" name="new_amount"
                                    class="form-input ltr:rounded-r-none rtl:rounded-l-none"
                                    value="{{ $advance->amount }}" min="1" step="1" required>
                                <span
                                    class="flex items-center justify-center border border-white-light bg-[#eee] px-3 font-semibold ltr:rounded-r-md ltr:border-l-0 rtl:rounded-l-md rtl:border-r-0 dark:border-[#17263c] dark:bg-[#1b2e4b]">د.ع</span>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2">
                                <span class="text-gray-700 dark:text-gray-300">سبب التعديل (اختياري)</span>
                            </label>
                            <textarea name="notes" class="form-input w-full" rows="2" placeholder="مثال: تجاوز الحد المسموح..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="closeEditApproveModal()">إلغاء</button>
                    <button type="submit" class="btn btn-warning">موافقة مع التعديل</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal الرفض -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <form action="{{ route('advances.reject', $advance) }}" method="POST">
                @csrf
                <div class="bg-danger text-white p-4 rounded-t-lg">
                    <h5 class="text-lg font-semibold">رفض السلفة</h5>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">سبب الرفض <span
                                    class="text-danger">*</span></span>
                        </label>
                        <textarea name="reason" class="form-input w-full" rows="3" required placeholder="يرجى ذكر سبب رفض السلفة"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeRejectModal()">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض السلفة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal الإلغاء -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <form action="{{ route('advances.cancel', $advance) }}" method="POST">
                @csrf
                <div class="bg-warning text-dark p-4 rounded-t-lg">
                    <h5 class="text-lg font-semibold">إلغاء السلفة</h5>
                </div>
                <div class="p-4">
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                        تنبيه: سيتم إلغاء السلفة وأي دفعات مسجلة ستبقى كما هي.
                    </div>
                    <div class="space-y-3">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">سبب الإلغاء <span
                                    class="text-danger">*</span></span>
                        </label>
                        <textarea name="reason" class="form-input w-full" rows="3" required placeholder="يرجى ذكر سبب إلغاء السلفة"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeCancelModal()">تراجع</button>
                    <button type="submit" class="btn btn-warning">إلغاء السلفة</button>
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

        function openRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEditApproveModal() {
            const modal = document.getElementById('editApproveModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditApproveModal() {
            const modal = document.getElementById('editApproveModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // إغلاق المودال عند الضغط خارجه
        document.getElementById('rejectModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });

        document.getElementById('editApproveModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditApproveModal();
        });

        document.getElementById('cancelModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCancelModal();
        });
    </script>
@endpush
