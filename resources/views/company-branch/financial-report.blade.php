@extends('layouts.app')

@section('page-title', 'تقرير مالي — الفرع')

@section('content')
    @php
        $refLabels = \App\Models\CompanyPaymentCardTransaction::$referenceTypes;
        $payLabels = \App\Models\CustomerPaymentRecord::$paymentMethods;
    @endphp
    <style>
        #branch-financial-report .stat-tile {
            border-radius: 0.5rem;
            padding: 1rem;
            color: #fff !important;
        }

        #branch-financial-report .stat-tile * {
            color: inherit !important;
        }

        #branch-financial-report .section-panel {
            border-radius: 0.5rem;
            padding: 1.25rem;
        }

        body:not(.dark) #branch-financial-report .section-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #1f2937;
        }

        body.dark #branch-financial-report .section-panel {
            background: #1f2937;
            border: 1px solid #374151;
            color: #e5e7eb;
        }
    </style>

    <div id="branch-financial-report" class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">📊 تقرير مالي — الفرع</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $branch->branch_name ?? 'الفرع' }}
                    @if ($from || $to)
                        <span class="font-mono text-xs">(
                            @if ($from)
                                من {{ $from }}
                            @endif
                            @if ($to)
                                إلى {{ $to }}
                            @endif
                            )</span>
                    @else
                        <span class="text-xs">(كل الفترات لحركات البطاقات والمقبوضات أدناه؛ أرصدة البطاقات حالية)</span>
                    @endif
                </p>
            </div>
            <a href="{{ $reportBackUrl ?? url('/home') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
        </div>

        @php
            $financialReportQuery = [];
            if (Auth::user()->isCompanyManager() || Auth::user()->isSuperAdmin()) {
                $financialReportQuery['branch_id'] = $financialBranchId ?? null;
            }
            $financialReportQuery = array_filter($financialReportQuery);
        @endphp
        <form method="get" action="{{ route('companyBranch.financial-report', $financialReportQuery) }}"
            class="section-panel flex flex-wrap items-end gap-4">
            @if (Auth::user()->isCompanyManager() || Auth::user()->isSuperAdmin())
                <input type="hidden" name="branch_id" value="{{ $financialBranchId }}">
            @endif
            <div>
                <label class="block text-sm font-medium mb-1">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $from }}"
                    class="form-input w-full sm:w-auto">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $to }}"
                    class="form-input w-full sm:w-auto">
            </div>
            <button type="submit" class="btn btn-primary">تطبيق</button>
            <a href="{{ route('companyBranch.financial-report', $financialReportQuery) }}" class="btn btn-outline-secondary">إعادة ضبط</a>
        </form>

        {{-- ملخص أعلى --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                <p class="text-xs opacity-90">بطاقات الفرع</p>
                <p class="text-2xl font-bold">{{ $summary['cards_total'] }}</p>
                <p class="text-xs opacity-90">نشطة: {{ $summary['cards_active'] }}</p>
            </div>
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#059669,#047857);">
                <p class="text-xs opacity-90">إجمالي الرصيد الحالي</p>
                <p class="text-xl font-bold">{{ number_format($summary['total_current_balance'], 0) }}</p>
                <p class="text-xs opacity-90">د.ع</p>
            </div>
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#0d9488,#0f766e);">
                <p class="text-xs opacity-90">إيداعات البطاقات (الفترة)</p>
                <p class="text-xl font-bold">{{ number_format($summary['period_deposits'], 0) }}</p>
                <p class="text-xs opacity-90">د.ع</p>
            </div>
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <p class="text-xs opacity-90">سحوبات البطاقات (الفترة)</p>
                <p class="text-xl font-bold">{{ number_format($summary['period_withdrawals'], 0) }}</p>
                <p class="text-xs opacity-90">د.ع</p>
            </div>
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
                <p class="text-xs opacity-90">صافي حركة البطاقات</p>
                <p class="text-xl font-bold">{{ number_format($summary['net_card_movement'], 0) }}</p>
                <p class="text-xs opacity-90">إيداع − سحب</p>
            </div>
            <div class="panel stat-tile text-center" style="background:linear-gradient(135deg,#ca8a04,#a16207);">
                <p class="text-xs opacity-90">تحصيلات الزبائن (الفترة)</p>
                <p class="text-xl font-bold">{{ number_format($summary['customer_collections_total'], 0) }}</p>
                <p class="text-xs opacity-90">د.ع</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="section-panel">
                <h3 class="font-bold mb-2 text-sm text-gray-500 dark:text-gray-400">دفعات الموردين (الفترة)</h3>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ number_format($summary['supplier_payments'], 0) }} <span class="text-sm font-normal">د.ع</span>
                </p>
            </div>
            <div class="section-panel">
                <h3 class="font-bold mb-2 text-sm text-gray-500 dark:text-gray-400">تكاليف صيانة السيارات المكتملة (الفترة)</h3>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                    {{ number_format($summary['maintenance_costs'], 0) }} <span class="text-sm font-normal">د.ع</span>
                </p>
            </div>
            <div class="section-panel">
                <h3 class="font-bold mb-2 text-sm text-gray-500 dark:text-gray-400">مجموع الأرصدة الافتتاحية (للبطاقات الحالية)</h3>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ number_format($summary['total_opening_balance'], 0) }} <span class="text-sm font-normal">د.ع</span>
                </p>
            </div>
        </div>

        {{-- التلف/الفقدان في المواد (الإتلاف) --}}
        <div class="section-panel">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-bold">🧯 تلف المواد (الإتلاف)</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        يعرض آخر 200 عملية إتلاف حسب الفترة المحددة.
                        @if ($from || $to)
                            <span class="font-mono">(
                                @if ($from)
                                    من {{ $from }}
                                @endif
                                @if ($to)
                                    إلى {{ $to }}
                                @endif
                                )</span>
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <div class="text-xs text-gray-500 dark:text-gray-400">إجمالي مبلغ الإتلاف (الفترة)</div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                        {{ number_format($summary['inventory_losses_total'] ?? 0, 0) }} <span class="text-sm font-normal">د.ع</span>
                    </div>
                </div>
            </div>

            @if (($inventoryLosses ?? collect())->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm">لا توجد عمليات إتلاف ضمن الفترة المحددة.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table-striped table-hover w-full min-w-[860px] text-sm">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-start">المادة</th>
                                <th class="text-center">النوع</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-center">سعر الوحدة</th>
                                <th class="text-center">الإجمالي</th>
                                <th class="text-start">المتلف</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">طباعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventoryLosses as $l)
                                <tr>
                                    <td class="text-center font-mono">{{ $l->id }}</td>
                                    <td class="font-semibold">{{ $l->material_name }}</td>
                                    <td class="text-center">{{ $l->material_type === 'chemical' ? 'كيميائية' : 'رئيسية' }}</td>
                                    <td class="text-center">
                                        {{ rtrim(rtrim(number_format((float) $l->quantity_lost, 4, '.', ''), '0'), '.') }}
                                        {{ $l->unit }}
                                    </td>
                                    <td class="text-center font-mono">{{ number_format((float) $l->unit_price_display, 0) }}</td>
                                    <td class="text-center font-mono font-bold text-red-600">{{ number_format((float) $l->total_cost, 0) }}</td>
                                    <td>{{ $l->creator?->fullname ?? '—' }}</td>
                                    <td class="text-center whitespace-nowrap">{{ ($l->reported_at ?? $l->created_at)->format('Y-m-d H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('warehouse.losses.print', $l->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-info" title="طباعة فاتورة الإتلاف">🖨</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- تلف شحنات الخرسانة (أوامر العمل) — لا يُحسب ضمن المنفذ --}}
        <div class="section-panel">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-bold">🚛 تلف شحنات الخرسانة</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        كمية وسعر تقديري حسب سعر أمر العمل؛ يظهر في أمر العمل ولا تُضاف كمية التالف إلى المنفذ.
                        @if ($from || $to)
                            <span class="font-mono">(
                                @if ($from)
                                    من {{ $from }}
                                @endif
                                @if ($to)
                                    إلى {{ $to }}
                                @endif
                                )</span>
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <div class="text-xs text-gray-500 dark:text-gray-400">إجمالي قيمة التلف (الفترة)</div>
                    <div class="text-2xl font-bold text-amber-700 dark:text-amber-400">
                        {{ number_format($summary['shipment_concrete_losses_total'] ?? 0, 0) }} <span class="text-sm font-normal">د.ع</span>
                    </div>
                </div>
            </div>

            @if (($shipmentConcreteLosses ?? collect())->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm">لا توجد تلفيات شحنات ضمن الفترة المحددة.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table-striped table-hover w-full min-w-[960px] text-sm">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-start">أمر العمل</th>
                                <th class="text-center">شحنة</th>
                                <th class="text-start">نوع الخرسانة</th>
                                <th class="text-start">نوع التلف</th>
                                <th class="text-center">كمية تالفة (م³)</th>
                                <th class="text-center">سعر المتر (د.ع)</th>
                                <th class="text-center">المبلغ</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">طباعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shipmentConcreteLosses as $wl)
                                @php
                                    $j = $wl->job;
                                    $unit = $j ? (float) ($j->unit_price ?? 0) : 0;
                                    $amt = (float) ($wl->actual_cost ?? $wl->estimated_cost ?? 0);
                                @endphp
                                <tr>
                                    <td class="text-center font-mono">{{ $wl->id }}</td>
                                    <td class="font-semibold">{{ $j->job_number ?? '—' }}</td>
                                    <td class="text-center font-mono">#{{ $wl->shipment?->shipment_number ?? '—' }}</td>
                                    <td>{{ $j?->concreteType?->classification ?? '—' }}</td>
                                    <td>{{ \App\Models\WorkLoss::TYPES[$wl->loss_type] ?? $wl->loss_type }}</td>
                                    <td class="text-center">{{ rtrim(rtrim(number_format((float) $wl->quantity_lost, 2, '.', ''), '0'), '.') }}</td>
                                    <td class="text-center font-mono">{{ number_format($unit, 0) }}</td>
                                    <td class="text-center font-mono font-bold text-amber-700 dark:text-amber-400">{{ number_format($amt, 0) }}</td>
                                    <td class="text-center whitespace-nowrap">{{ $wl->reported_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('companyBranch.workShipmentLoss.print', $wl->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-warning" title="طباعة سند التلف">🖨</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- تفصيل البطاقات --}}
        <div class="section-panel overflow-x-auto">
            <h2 class="text-lg font-bold mb-4">💳 بطاقات الدفع في الفرع</h2>
            @if ($cardsWithPeriod->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">لا توجد بطاقات دفع مسجّلة لهذا الفرع.</p>
            @else
                <table class="table-striped table-hover w-full min-w-[720px]">
                    <thead>
                        <tr>
                            <th class="text-start">البطاقة</th>
                            <th class="text-start">النوع</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الرصيد الحالي</th>
                            <th class="text-center">إيداعات الفترة</th>
                            <th class="text-center">سحوبات الفترة</th>
                            <th class="text-center">عدد الحركات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cardsWithPeriod as $row)
                            @php $c = $row['card']; @endphp
                            <tr>
                                <td class="font-semibold">{{ $c->card_name }}</td>
                                <td>{{ \App\Models\CompanyPaymentCard::$cardTypes[$c->card_type] ?? $c->card_type }}</td>
                                <td class="text-center">
                                    @if ($c->is_active)
                                        <span class="badge bg-success/20 text-success">نشطة</span>
                                    @else
                                        <span class="badge bg-gray-500/20">موقوفة</span>
                                    @endif
                                </td>
                                <td class="text-center font-mono">{{ number_format($c->current_balance, 0) }}</td>
                                <td class="text-center text-green-600 dark:text-green-400 font-semibold">
                                    {{ number_format($row['period_deposits'], 0) }}</td>
                                <td class="text-center text-red-600 dark:text-red-400 font-semibold">
                                    {{ number_format($row['period_withdrawals'], 0) }}</td>
                                <td class="text-center">{{ $row['period_tx_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="section-panel">
                <h2 class="text-lg font-bold mb-3">⬇️ الإيداعات حسب مصدر الحركة</h2>
                @if ($depositsByRef->isEmpty())
                    <p class="text-gray-500 text-sm">لا توجد إيداعات في الفترة المحددة.</p>
                @else
                    <table class="table-striped w-full">
                        <thead>
                            <tr>
                                <th class="text-start">المصدر</th>
                                <th class="text-center">العدد</th>
                                <th class="text-center">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($depositsByRef as $row)
                                <tr>
                                    <td>{{ $refLabels[$row->reference_type] ?? ($row->reference_type ?: '—') }}</td>
                                    <td class="text-center">{{ $row->cnt }}</td>
                                    <td class="text-center font-semibold text-green-600">
                                        {{ number_format($row->total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="section-panel">
                <h2 class="text-lg font-bold mb-3">⬆️ السحوبات حسب نوع المرجع</h2>
                @if ($withdrawalsByRef->isEmpty())
                    <p class="text-gray-500 text-sm">لا توجد سحوبات في الفترة المحددة.</p>
                @else
                    <table class="table-striped w-full">
                        <thead>
                            <tr>
                                <th class="text-start">المرجع</th>
                                <th class="text-center">العدد</th>
                                <th class="text-center">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawalsByRef as $row)
                                <tr>
                                    <td>{{ $refLabels[$row->reference_type] ?? ($row->reference_type ?: '—') }}</td>
                                    <td class="text-center">{{ $row->cnt }}</td>
                                    <td class="text-center font-semibold text-red-600">
                                        {{ number_format($row->total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        @if ($customerCollections->isNotEmpty())
            <div class="section-panel">
                <h2 class="text-lg font-bold mb-3">👤 تحصيلات الزبائن حسب طريقة الدفع (الفترة)</h2>
                <table class="table-striped w-full max-w-xl">
                    <thead>
                        <tr>
                            <th class="text-start">الطريقة</th>
                            <th class="text-center">العمليات</th>
                            <th class="text-center">المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerCollections as $row)
                            <tr>
                                <td>{{ $payLabels[$row->payment_method] ?? $row->payment_method }}</td>
                                <td class="text-center">{{ $row->cnt }}</td>
                                <td class="text-center font-semibold">{{ number_format($row->total, 0) }} د.ع</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section-panel overflow-x-auto">
            <h2 class="text-lg font-bold mb-4">📜 آخر حركات البطاقات (حتى 150 سجل)</h2>
            @if ($recentTransactions->isEmpty())
                <p class="text-gray-500 text-sm">لا توجد حركات في الفترة المحددة.</p>
            @else
                <table class="table-striped table-hover w-full min-w-[880px] text-sm">
                    <thead>
                        <tr>
                            <th class="text-center">التاريخ</th>
                            <th class="text-start">البطاقة</th>
                            <th class="text-center">النوع</th>
                            <th class="text-center">المبلغ</th>
                            <th class="text-start">المرجع</th>
                            <th class="text-start">الوصف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTransactions as $tx)
                            <tr>
                                <td class="text-center whitespace-nowrap">{{ $tx->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td>{{ $tx->paymentCard->card_name ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($tx->type === 'deposit')
                                        <span class="text-green-600 font-semibold">إيداع</span>
                                    @else
                                        <span class="text-red-600 font-semibold">سحب</span>
                                    @endif
                                </td>
                                <td class="text-center font-mono font-bold">{{ number_format($tx->amount, 0) }}</td>
                                <td>{{ $refLabels[$tx->reference_type] ?? ($tx->reference_type ?: '—') }}</td>
                                <td class="max-w-xs truncate" title="{{ $tx->description }}">{{ $tx->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
