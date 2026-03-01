@extends('layouts.app')

@section('page-title', 'أنواع الموظفين')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">أنواع الموظفين</h1>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Employee Type Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إضافة نوع موظف جديد</h3>
                <form action="{{ route('admin.employee-types.store') }}" method="POST" class="flex flex-wrap gap-4 items-end">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">اسم النوع *</label>
                        <input type="text" name="name" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <button type="submit"
                        class="btn btn-info text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        إضافة
                    </button>
                </form>
            </div>

            <!-- Employee Types Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">اسم النوع</th>
                                <th scope="col" class="px-4 py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $index => $type)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    id="type-row-{{ $type->id }}">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $type->name }}</span>
                                        <input type="text"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1"
                                            value="{{ $type->name }}" name="name">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 view-mode">
                                            <button onclick="editType({{ $type->id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="edit-mode hidden flex gap-2">
                                            <button onclick="saveType({{ $type->id }})"
                                                class="text-green-600 hover:text-green-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button onclick="cancelEdit({{ $type->id }})"
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
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">لا يوجد أنواع</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editType(id) {
            const row = document.getElementById('type-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
        }

        function cancelEdit(id) {
            const row = document.getElementById('type-row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
        }

        function saveType(id) {
            const row = document.getElementById('type-row-' + id);
            const name = row.querySelector('input[name="name"]').value;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('admin/employee-types') }}/' + id;
            form.innerHTML = `
        @csrf
        @method('PUT')
        <input type="hidden" name="name" value="${name}">
    `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
