@extends('layouts.app')

@section('page-title', 'المدن والمناطق')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">إدارة المدن والمناطق</h1>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add City Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إضافة مدينة جديدة</h3>
                <form action="{{ route('admin.cities.store') }}" method="POST" class="flex flex-wrap gap-4 items-end">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الاسم بالعربي *</label>
                        <input type="text" name="name_ar" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الاسم بالإنجليزي</label>
                        <input type="text" name="name_en"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <button type="submit"
                        class="font-medium rounded-lg text-sm px-5 py-2.5 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800 bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700"
                        style="background-color:#2563eb;color:#fff;">
                        إضافة
                    </button>
                </form>
            </div>

            <!-- Cities Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">الاسم بالعربي</th>
                                <th scope="col" class="px-4 py-3">الاسم بالإنجليزي</th>
                                <th scope="col" class="px-4 py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cities as $index => $city)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    id="city-row-{{ $city->id }}">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $city->name_ar }}</span>
                                        <input type="text"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1"
                                            value="{{ $city->name_ar }}" name="name_ar">
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="view-mode">{{ $city->name_en ?? '-' }}</span>
                                        <input type="text"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1"
                                            value="{{ $city->name_en }}" name="name_en">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 view-mode">
                                            <button onclick="editCity({{ $city->id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="edit-mode hidden flex gap-2">
                                            <button onclick="saveCity({{ $city->id }})"
                                                class="text-green-600 hover:text-green-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button onclick="cancelEdit({{ $city->id }})"
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
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">لا يوجد مدن</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editCity(id) {
            const row = document.getElementById('city-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
        }

        function cancelEdit(id) {
            const row = document.getElementById('city-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
        }

        function saveCity(id) {
            const row = document.getElementById('city-row-' + id);
            const nameAr = row.querySelector('input[name="name_ar"]').value;
            const nameEn = row.querySelector('input[name="name_en"]').value;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('admin/cities') }}/' + id;
            form.innerHTML = `
        @csrf
        @method('PUT')
        <input type="hidden" name="name_ar" value="${nameAr}">
        <input type="hidden" name="name_en" value="${nameEn}">
    `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
