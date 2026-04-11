@extends('layouts.app')

@section('page-title', 'إضافة تالف')

@section('content')
    <div class="panel mt-6 max-w-3xl mx-auto">
        <div class="flex items-center justify-between gap-3 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">🧯 إضافة تالف (مادة كيميائية)</h3>
            <a href="{{ url('warehouse/Branchlistchemicals') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 rounded border dark:border-gray-700">
                <div class="text-xs text-gray-500 mb-1">المادة</div>
                <div class="font-bold">{{ $chemical->name }}</div>
                <div class="text-xs text-gray-500 mt-1">المعرف: <span class="font-mono">{{ $chemical->id }}</span></div>
            </div>
            <div class="p-4 rounded border dark:border-gray-700">
                <div class="text-xs text-gray-500 mb-1">المتوفر</div>
                <div class="font-bold">
                    {{ rtrim(rtrim(number_format($qtyDisplayAvailable, 4, '.', ''), '0'), '.') }}
                    {{ $chemical->MeasurementUnit?->name ?? $chemical->unit }}
                </div>
                <div class="text-xs text-gray-500 mt-1">سعر الوحدة: {{ number_format($unitPriceDisplay, 0) }} د.ع</div>
            </div>
        </div>

        <form method="POST" action="{{ route('warehouse.update', $chemical->id) }}" id="lossForm" target="_blank">
            @csrf
            @method('PUT')
            <input type="hidden" name="active" value="ReportChemicalLoss">

            <div class="mb-4">
                <label class="block mb-1 font-medium">الكمية التالفة <span class="text-danger">*</span></label>
                <input type="number" step="0.0001" min="0" name="loss_quantity" id="loss_quantity" class="form-input w-full"
                    value="{{ old('loss_quantity') }}" required>
                @error('loss_quantity')
                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-xs text-gray-500 mb-1">سعر الوحدة</div>
                    <div class="font-bold">
                        <span id="unit_price_display">{{ number_format($unitPriceDisplay, 0) }}</span>
                        <span class="text-xs text-gray-500">د.ع</span>
                    </div>
                </div>
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-xs text-gray-500 mb-1">الإجمالي المتوقع للتالف</div>
                    <div class="font-bold text-danger">
                        <span id="loss_total_display">0</span>
                        <span class="text-xs text-gray-500">د.ع</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">ملاحظة</label>
                <textarea name="note" rows="3" class="form-input w-full" placeholder="اختياري">{{ old('note') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="btn btn-danger" id="submitLossBtn">تسجيل التالف</button>
            </div>
        </form>
    </div>

    <script>
        (function() {
            const unitPrice = Number(@json((float) $unitPriceDisplay)) || 0;
            const qtyEl = document.getElementById('loss_quantity');
            const totalEl = document.getElementById('loss_total_display');

            function format(n) {
                try { return Number(n || 0).toLocaleString('en-US'); } catch (e) { return String(n || 0); }
            }

            function recalc() {
                const qty = Number(qtyEl?.value || 0);
                const total = unitPrice * qty;
                if (totalEl) totalEl.textContent = format(Math.round(total));
            }

            qtyEl?.addEventListener('input', recalc);
            recalc();
        })();

        // بعد الضغط على تسجيل الإتلاف: تحديث الصفحة بعد 7 ثواني
        (function() {
            const form = document.getElementById('lossForm');
            const btn = document.getElementById('submitLossBtn');
            if (!form || !btn) return;

            btn.addEventListener('click', function() {
                setTimeout(function() {
                    window.location.reload();
                }, 7000);
            });
        })();
    </script>
@endsection

