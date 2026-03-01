{{-- فلتر التقارير --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ url()->current() }}" id="reportFilters">
            <div class="row g-3">
                @if (isset($showDateRange) && $showDateRange)
                    {{-- فلتر الفترات الجاهزة --}}
                    <div class="col-md-3">
                        <label class="form-label">فترة سريعة</label>
                        <select name="preset" class="form-select" onchange="applyPreset(this.value)">
                            <option value="">-- اختر فترة --</option>
                            @foreach ($presets ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ request('preset') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- من تاريخ --}}
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date', $filters['from_date'] ?? '') }}">
                    </div>

                    {{-- إلى تاريخ --}}
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date', $filters['to_date'] ?? '') }}">
                    </div>
                @endif

                @if (isset($showBranchFilter) && $showBranchFilter)
                    {{-- فلتر الفرع --}}
                    <div class="col-md-3">
                        <label class="form-label">الفرع</label>
                        <select name="branch_id" class="form-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if (isset($showStatusFilter) && $showStatusFilter)
                    {{-- فلتر الحالة --}}
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            @foreach ($statuses ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @yield('extra-filters')

                {{-- أزرار الإجراءات --}}
                <div class="col-md-12">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            عرض التقرير
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>
                            إعادة تعيين
                        </a>
                        @if (isset($showExport) && $showExport)
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-1"></i>
                                    تصدير
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route($exportRoute ?? 'reports.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                                            <i class="fas fa-file-excel me-2 text-success"></i>
                                            Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route($exportRoute ?? 'reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                                            <i class="fas fa-file-pdf me-2 text-danger"></i>
                                            PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <a href="{{ route($printRoute ?? 'reports.print', array_merge(['type' => $reportType ?? 'general'], request()->all())) }}"
                            class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-print me-1"></i>
                            طباعة
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        function applyPreset(preset) {
            if (!preset || preset === 'custom') return;

            const presets = {
                'today': {
                    from: dayjs().format('YYYY-MM-DD'),
                    to: dayjs().format('YYYY-MM-DD')
                },
                'yesterday': {
                    from: dayjs().subtract(1, 'day').format('YYYY-MM-DD'),
                    to: dayjs().subtract(1, 'day').format('YYYY-MM-DD')
                },
                'this_week': {
                    from: dayjs().startOf('week').format('YYYY-MM-DD'),
                    to: dayjs().endOf('week').format('YYYY-MM-DD')
                },
                'last_week': {
                    from: dayjs().subtract(1, 'week').startOf('week').format('YYYY-MM-DD'),
                    to: dayjs().subtract(1, 'week').endOf('week').format('YYYY-MM-DD')
                },
                'this_month': {
                    from: dayjs().startOf('month').format('YYYY-MM-DD'),
                    to: dayjs().endOf('month').format('YYYY-MM-DD')
                },
                'last_month': {
                    from: dayjs().subtract(1, 'month').startOf('month').format('YYYY-MM-DD'),
                    to: dayjs().subtract(1, 'month').endOf('month').format('YYYY-MM-DD')
                },
                'this_quarter': {
                    from: dayjs().startOf('quarter').format('YYYY-MM-DD'),
                    to: dayjs().endOf('quarter').format('YYYY-MM-DD')
                },
                'this_year': {
                    from: dayjs().startOf('year').format('YYYY-MM-DD'),
                    to: dayjs().endOf('year').format('YYYY-MM-DD')
                },
            };

            if (presets[preset]) {
                document.querySelector('input[name="from_date"]').value = presets[preset].from;
                document.querySelector('input[name="to_date"]').value = presets[preset].to;
            }
        }
    </script>
@endpush
