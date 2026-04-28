@extends('layouts.app')

@section('page-title', 'إضافة شحنة')

@section('content')
    <div class="panel mt-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-xl font-bold dark:text-white-light">🚛 إضافة شحنة جديدة</h3>
                <p class="text-gray-500 text-sm mt-1">أمر العمل: <span class="font-semibold text-primary">{{ $job->job_number }}</span></p>
            </div>
            <a href="{{ route('companyBranch.workJob.view', $job->id) }}" class="btn btn-outline-secondary">رجوع</a>
        </div>

        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-lg border p-3">
                <div class="text-xs text-gray-500">الكمية الكلية</div>
                <div class="font-bold">{{ number_format((float) $job->total_quantity, 2) }} م³</div>
            </div>
            <div class="rounded-lg border p-3">
                <div class="text-xs text-gray-500">المتبقي للتخطيط</div>
                <div class="font-bold text-primary">{{ number_format((float) $remainingForNewShipments, 2) }} م³</div>
            </div>
            <div class="rounded-lg border p-3">
                <div class="text-xs text-gray-500">البَم المخصص</div>
                <div class="font-bold">{{ $job->defaultPump->car_number ?? 'غير محدد' }}</div>
            </div>
        </div>

        @if (!$job->default_pump_id)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900 text-sm">
                يجب تخصيص بَم أولاً قبل إضافة الشحنات.
            </div>
        @elseif ($remainingForNewShipments <= 0)
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 text-sm">
                لا توجد كمية متبقية لإضافة شحنة جديدة.
            </div>
        @else
            <form action="{{ route('companyBranch.workJob.addShipment', $job->id) }}" method="POST" id="shipmentForm">
                @csrf

                <div class="mt-4">
                    <h4 class="font-semibold mb-2">اختيار الخباطات (يمكن اختيار أكثر من واحدة)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($mixers as $mixer)
                            @php
                                $capacity = $mixer->mixer_capacity ?? ($mixer->carType->capacity ?? 0);
                                $disabled = ($mixer->is_in_maintenance ?? false) || $mixer->is_busy || $mixer->is_reserved || $capacity <= 0;
                            @endphp
                            <button type="button"
                                class="mixer-card rounded-lg border p-3 text-right transition {{ $disabled ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-900/30' : 'hover:border-primary' }}"
                                data-id="{{ $mixer->id }}" data-capacity="{{ $capacity }}"
                                data-driver-id="{{ $mixer->driver_id ?? '' }}"
                                data-label="{{ $mixer->car_model }} (#{{ $mixer->car_number }})"
                                {{ $disabled ? 'disabled' : '' }}>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-bold">{{ $mixer->car_model }}</div>
                                        <div class="text-xs text-gray-500"># {{ $mixer->car_number }}</div>
                                    </div>
                                    <span class="text-xs {{ $disabled ? 'text-red-500' : 'text-green-600' }}">
                                        {{ $disabled ? ($mixer->status_text ?? 'غير متاحة') : 'متاحة' }}
                                    </span>
                                </div>
                                <div class="text-xs mt-2">سعة الخباطة: <span class="font-semibold">{{ $capacity }} م³</span></div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5">
                    <h4 class="font-semibold mb-2">التعيينات المختارة</h4>
                    <div id="selectedAssignments" class="space-y-4"></div>
                    <p class="text-xs text-gray-500 mt-2">لكل خباطة اختر السائق بنمط كرت وحدد الكمية.</p>
                </div>

                <div class="mt-5 rounded-lg border p-3 bg-gray-50 dark:bg-gray-900/20">
                    <div class="text-sm">
                        مجموع الكميات المختارة:
                        <span id="totalQtyText" class="font-bold text-primary">0</span> م³
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        الحد الأقصى: {{ number_format((float) $remainingForNewShipments, 2) }} م³
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('companyBranch.workJob.view', $job->id) }}" class="btn btn-outline-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ الشحنة</button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @php
        $driversJs = $drivers
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->username ?? $d->fullname ?? $d->name,
                    'sub' => $d->fullname ?? '',
                ];
            })
            ->values();
    @endphp
    <script>
        const maxRemaining = parseFloat(@json((float) $remainingForNewShipments));
        const assignmentsContainer = document.getElementById('selectedAssignments');
        const totalQtyText = document.getElementById('totalQtyText');
        const selected = {};
        const drivers = @json($driversJs);

        function computeTotalQty() {
            let total = 0;
            Object.values(selected).forEach((s) => {
                total += parseFloat(s.quantity || 0);
            });
            totalQtyText.textContent = total.toFixed(2).replace(/\.00$/, '');
            totalQtyText.classList.toggle('text-danger', total > maxRemaining + 0.0001);
            totalQtyText.classList.toggle('text-primary', total <= maxRemaining + 0.0001);
            return total;
        }

        function renderAssignments() {
            assignmentsContainer.innerHTML = '';
            Object.values(selected).forEach((item) => {
                const block = document.createElement('div');
                block.className = 'rounded-lg border p-3';
                const title = document.createElement('div');
                title.className = 'font-semibold mb-2';
                title.textContent = item.label;
                block.appendChild(title);

                const hiddenMixer = document.createElement('input');
                hiddenMixer.type = 'hidden';
                hiddenMixer.name = `shipments[${item.key}][mixer_id]`;
                hiddenMixer.value = item.mixerId;
                block.appendChild(hiddenMixer);

                const qtyWrap = document.createElement('div');
                qtyWrap.className = 'mb-3';
                qtyWrap.innerHTML =
                    `<label class="text-xs text-gray-500 block mb-1">الكمية (م³)</label>
                     <input type="number" name="shipments[${item.key}][quantity]" step="0.5" min="0.5" class="form-input w-full md:w-56" value="${item.quantity}" required />`;
                block.appendChild(qtyWrap);

                qtyWrap.querySelector('input').addEventListener('input', function() {
                    item.quantity = this.value || '';
                    computeTotalQty();
                });

                const driverTitle = document.createElement('div');
                driverTitle.className = 'text-xs text-gray-500 mb-2';
                driverTitle.textContent = 'اختيار السائق (كرد فيو)';
                block.appendChild(driverTitle);

                const driversGrid = document.createElement('div');
                driversGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2';

                drivers.forEach((d) => {
                    const lbl = document.createElement('label');
                    lbl.className = 'rounded-md border p-2 cursor-pointer hover:border-primary transition';
                    const checked = String(item.driverId || '') === String(d.id);
                    if (checked) {
                        lbl.classList.add('border-primary', 'bg-primary/10');
                    }
                    lbl.innerHTML =
                        `<input type="radio" name="shipments[${item.key}][driver_id]" value="${d.id}" class="form-radio text-primary" ${checked ? 'checked' : ''} required>
                         <div class="mt-1"><div class="font-semibold text-sm">${d.name}</div><div class="text-xs text-gray-500">${d.sub || ''}</div></div>`;
                    lbl.querySelector('input').addEventListener('change', function() {
                        item.driverId = d.id;
                        renderAssignments();
                    });
                    driversGrid.appendChild(lbl);
                });

                block.appendChild(driversGrid);
                assignmentsContainer.appendChild(block);
            });
            computeTotalQty();
        }

        document.querySelectorAll('.mixer-card').forEach((card) => {
            card.addEventListener('click', function() {
                if (this.disabled) return;
                const key = this.dataset.id;
                if (selected[key]) {
                    delete selected[key];
                    this.classList.remove('border-primary', 'bg-primary/10');
                } else {
                    selected[key] = {
                        key: key,
                        mixerId: this.dataset.id,
                        label: this.dataset.label,
                        quantity: this.dataset.capacity || '',
                        driverId: this.dataset.driverId || '',
                    };
                    this.classList.add('border-primary', 'bg-primary/10');
                }
                renderAssignments();
            });
        });

        document.getElementById('shipmentForm')?.addEventListener('submit', function(e) {
            const keys = Object.keys(selected);
            if (keys.length === 0) {
                e.preventDefault();
                alert('يرجى اختيار خباطة واحدة على الأقل.');
                return;
            }

            const total = computeTotalQty();
            if (total > maxRemaining + 0.0001) {
                e.preventDefault();
                alert('مجموع الكميات يتجاوز الحد المسموح.');
                return;
            }
        });
    </script>
@endpush
