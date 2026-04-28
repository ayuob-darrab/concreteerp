@extends('layouts.app')

@section('page-title', 'أنواع السيارات')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">أنواع السيارات</h1>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Add Car Type Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">إضافة نوع سيارة جديد</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">يُولَّد <span class="font-mono">الكود (CT-…)</span> تلقائياً عند الحفظ — لا حاجة لإدخاله.</p>
                <form action="{{ route('admin.car-types.store') }}" method="POST"
                    class="flex flex-nowrap items-end gap-3 overflow-x-auto pb-1 [scrollbar-color:rgba(0,0,0,.2)_transparent]">
                    @csrf
                    <div class="min-w-[200px] max-w-[min(100%,24rem)] flex-1 shrink-0">
                        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">اسم النوع *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 whitespace-normal">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-32 min-w-[7rem] shrink-0">
                        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">سعة (م³)</label>
                        <input type="number" name="capacity" value="{{ old('capacity') }}" step="0.01" min="0"
                            placeholder="—"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('capacity') border-red-500 @enderror">
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600 whitespace-normal">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-36 min-w-[8rem] shrink-0">
                        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">طول الخرطوم (م)</label>
                        <input type="number" name="hose_length" value="{{ old('hose_length') }}" step="0.01" min="0"
                            placeholder="—"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('hose_length') border-red-500 @enderror">
                        @error('hose_length')
                            <p class="mt-1 text-sm text-red-600 whitespace-normal">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="shrink-0 self-end">
                        <button type="submit"
                            class="whitespace-nowrap text-white bg-primary hover:bg-primary/90 focus:ring-4 focus:ring-primary/30 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none shadow-md">
                            إضافة
                        </button>
                    </div>
                </form>
            </div>

            <!-- Car Types Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">كود الشركة</th>
                                <th scope="col" class="px-4 py-3">الكود</th>
                                <th scope="col" class="px-4 py-3">اسم النوع</th>
                                <th scope="col" class="px-4 py-3">السعة (م³)</th>
                                <th scope="col" class="px-4 py-3">طول الخرطوم (م)</th>
                                <th scope="col" class="px-4 py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $index => $type)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    id="cartype-row-{{ $type->id }}">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">
                                            {{ $type->company_code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $type->code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $type->name }}</span>
                                        <input type="text"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full max-w-xs"
                                            value="{{ $type->name }}" name="name">
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $type->capacity !== null ? number_format((float) $type->capacity, 2) : '—' }}</span>
                                        <input type="number" step="0.01" min="0" name="capacity"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full max-w-[120px]"
                                            value="{{ $type->capacity }}">
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $type->hose_length !== null ? number_format((float) $type->hose_length, 2) : '—' }}</span>
                                        <input type="number" step="0.01" min="0" name="hose_length"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full max-w-[120px]"
                                            value="{{ $type->hose_length }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 view-mode">
                                            <button onclick="editCarType({{ $type->id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.car-types.delete', $type->id) }}" method="POST"
                                                class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="edit-mode hidden flex gap-2">
                                            <button onclick="saveCarType({{ $type->id }})"
                                                class="text-green-600 hover:text-green-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button onclick="cancelCarEdit({{ $type->id }})"
                                                class="text-gray-600 hover:text-gray-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا يوجد أنواع</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editCarType(id) {
            const row = document.getElementById('cartype-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
        }

        function cancelCarEdit(id) {
            const row = document.getElementById('cartype-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
        }

        function saveCarType(id) {
            const row = document.getElementById('cartype-row-' + id);
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('car-types') }}/' + id;
            const add = (n, v) => {
                const i = document.createElement('input');
                i.type = 'hidden';
                i.name = n;
                i.value = v;
                form.appendChild(i);
            };
            add('_token', '{{ csrf_token() }}');
            add('_method', 'PUT');
            add('name', row.querySelector('input[name="name"]').value);
            add('capacity', row.querySelector('input[name="capacity"]') ? row.querySelector('input[name="capacity"]')
                .value : '');
            add('hose_length', row.querySelector('input[name="hose_length"]') ? row.querySelector(
                'input[name="hose_length"]').value : '');
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
