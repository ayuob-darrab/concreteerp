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
                        <input type="text" name="name" required value="{{ old('name') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="w-40 min-w-[140px]">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الرمز <span class="text-red-500">*</span></label>
                        <input type="text" name="code" required value="{{ old('code') }}" dir="ltr" placeholder="مثال: ENG"
                            pattern="[A-Z0-9_]+" maxlength="32" title="أحرف إنجليزية كبيرة وأرقام و _ فقط"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white uppercase">
                        <p class="text-xs text-gray-500 mt-1">أحرف إنجليزية كبيرة وأرقام و _ فقط</p>
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">الوصف</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <button type="submit"
                        class="btn btn-info text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        إضافة
                    </button>
                </form>
                @if ($errors->any())
                    <div class="p-4 mt-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Employee Types Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">الرمز</th>
                                <th scope="col" class="px-4 py-3">اسم النوع</th>
                                <th scope="col" class="px-4 py-3">الوصف</th>
                                <th scope="col" class="px-4 py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $index => $type)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    id="type-row-{{ $type->id }}">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-800 dark:text-gray-200" dir="ltr">
                                        <span class="view-mode">{{ $type->code ?? '—' }}</span>
                                        <input type="text" required
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full max-w-[120px]"
                                            value="{{ $type->code }}" name="code" dir="ltr" placeholder="رمز" maxlength="32" pattern="[A-Z0-9_]+" title="أحرف كبيرة وأرقام و _ فقط">
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <span class="view-mode">{{ $type->name }}</span>
                                        <input type="text"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full"
                                            value="{{ $type->name }}" name="name">
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs max-w-xs">
                                        <span class="view-mode line-clamp-2">{{ $type->description ?? '—' }}</span>
                                        <textarea name="description" rows="2"
                                            class="edit-mode hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 w-full">{{ $type->description }}</textarea>
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
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">لا يوجد أنواع</td>
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
            const code = (row.querySelector('input[name="code"]').value || '').trim().toUpperCase();
            const description = row.querySelector('textarea[name="description"]').value;

            if (!code) {
                alert('الرمز مطلوب.');
                return;
            }
            if (!/^[A-Z0-9_]+$/.test(code)) {
                alert('الرمز: أحرف إنجليزية كبيرة وأرقام وشرطة سفلية فقط.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('admin/employee-types') }}/' + id;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);

            const iName = document.createElement('input');
            iName.type = 'hidden';
            iName.name = 'name';
            iName.value = name;
            form.appendChild(iName);

            const iCode = document.createElement('input');
            iCode.type = 'hidden';
            iCode.name = 'code';
            iCode.value = code;
            form.appendChild(iCode);

            row.querySelector('input[name="code"]').value = code;

            const iDesc = document.createElement('input');
            iDesc.type = 'hidden';
            iDesc.name = 'description';
            iDesc.value = description;
            form.appendChild(iDesc);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
