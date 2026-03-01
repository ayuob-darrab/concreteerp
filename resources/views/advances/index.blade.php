@extends('layouts.app')

@section('page-title', 'نظام السلف والقروض')

@section('content')
    <div class="grid grid-cols-1 gap-6">

        {{-- البحث والتصفية --}}
        <div class="panel">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    📋 جميع السلف
                </h5>
                <div class="flex gap-2">
                    <a href="{{ route('advances.index', ['status' => 'completed']) }}"
                        class="btn btn-success flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>المكتملة</span>
                    </a>
                </div>
            </div>

            {{-- البحث --}}
            <form method="GET" action="{{ route('advances.index') }}" class="mb-5">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex gap-3 items-end flex-wrap">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-medium mb-2">رقم السلفة</label>
                        <input type="text" name="search" class="form-input" placeholder="أدخل رقم السلفة..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-medium mb-2">المستفيد</label>
                        <input type="text" name="beneficiary" class="form-input" placeholder="اسم المستفيد..."
                            value="{{ request('beneficiary') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        🔍 بحث
                    </button>
                    <a href="{{ route('advances.index', request('status') ? ['status' => request('status')] : []) }}"
                        class="btn btn-outline-secondary">
                        🔄 مسح
                    </a>
                </div>
            </form>

            {{-- جدول السلف --}}
            @if ($advances->count() > 0)
                <div class="table-responsive">
                    <table class="table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم السلفة</th>
                                <th>المستفيد</th>
                                <th>نوع المستفيد</th>
                                <th>المبلغ</th>
                                <th>المسدد</th>
                                <th>تاريخ الإكمال</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advances as $advance)
                                <tr>
                                    <td>{{ $loop->iteration + ($advances->currentPage() - 1) * $advances->perPage() }}</td>
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
                                    </td>
                                    <td class="font-semibold">{{ number_format($advance->amount) }} د.ع</td>
                                    <td class="text-success font-semibold">{{ number_format($advance->paid_amount) }} د.ع
                                    </td>
                                    <td>{{ $advance->completed_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('advances.show', $advance) }}"
                                                class="btn btn-outline-info btn-sm" title="عرض">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('advances.print', $advance) }}" target="_blank"
                                                class="btn btn-outline-secondary btn-sm" title="طباعة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                    <path
                                                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                                    </path>
                                                    <rect x="6" y="14" width="12" height="8"></rect>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-5 flex justify-center">
                    {{ $advances->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-10">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <h4 class="text-lg font-semibold text-gray-500 mb-2">لا توجد سلف</h4>
                    <p class="text-gray-400">لم يتم العثور على أي سلف تطابق معايير البحث</p>
                    <a href="{{ route('advances.create') }}" class="btn btn-primary mt-4">
                        ➕ إضافة سلفة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
