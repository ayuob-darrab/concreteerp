@extends('layouts.app')

@section('page-title', 'الأدوار والصلاحيات')

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">الأدوار والصلاحيات</h1>
                <button onclick="openAddRoleModal()"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة دور جديد
                </button>
            </div>

            <!-- رسائل النجاح والخطأ -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($roles as $role)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 relative">
                        @if ($role->is_system ?? false)
                            <span
                                class="absolute top-2 left-2 bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">نظام</span>
                        @else
                            <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST"
                                class="absolute top-2 left-2" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="حذف">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $role->name }}</h3>
                            <span
                                class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                {{ $role->user_count ?? 0 }} مستخدم
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $role->description }}</p>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">كود الدور:</span>
                            <code class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $role->code }}</code>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Permissions Info -->
            <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">نظام الصلاحيات</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 bg-red-500 rounded-full mt-1.5"></div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">سوبر أدمن (SA)</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">صلاحيات كاملة على جميع الشركات والنظام
                                بأكمله</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mt-1.5"></div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">مدير شركة (CM)</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">إدارة شركة كاملة مع جميع الفروع والموظفين
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full mt-1.5"></div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">مدير فرع (BM)</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">إدارة فرع واحد فقط مع صلاحيات محدودة</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mt-1.5"></div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">مقاول</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">حساب مقاول لمتابعة الطلبات والمشاريع</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mt-1.5"></div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">مندوب</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">حساب مندوب للمبيعات والتوصيل</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal إضافة دور جديد -->
    <div id="addRoleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- خلفية داكنة -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddRoleModal()"></div>

        <!-- محتوى Modal -->
        <div class="fixed inset-x-0 top-0 flex justify-center p-4 pt-20">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all">
                <!-- رأس Modal -->
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">إضافة دور جديد</h3>
                    </div>
                    <button onclick="closeAddRoleModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- نموذج الإضافة -->
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="p-5 space-y-5">
                        <!-- رمز الدور -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                رمز الدور <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white uppercase transition-colors"
                                placeholder="مثال: DRIVER" pattern="[a-zA-Z0-9_]+" title="أحرف إنجليزية وأرقام فقط"
                                maxlength="20">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                أحرف إنجليزية وأرقام فقط (بدون مسافات)
                            </p>
                        </div>

                        <!-- اسم الدور -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                اسم الدور <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                                placeholder="مثال: سائق" maxlength="100">
                        </div>

                        <!-- وصف الدور -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                وصف الدور <span class="text-gray-400 text-xs font-normal">(اختياري)</span>
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none transition-colors"
                                placeholder="وصف مختصر للدور وصلاحياته..." maxlength="500"></textarea>
                        </div>
                    </div>

                    <!-- أزرار التحكم -->
                    <div
                        class="flex gap-3 p-5 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="flex-1 px-5 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 font-semibold transition-all hover:shadow-lg hover:shadow-primary/25 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            إنشاء الدور
                        </button>
                        <button type="button" onclick="closeAddRoleModal()"
                            class="px-5 py-3 bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-500 font-semibold border-2 border-gray-200 dark:border-gray-500 transition-colors">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddRoleModal() {
            document.getElementById('addRoleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddRoleModal() {
            document.getElementById('addRoleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // إغلاق Modal بالضغط على Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddRoleModal();
            }
        });
    </script>
@endsection
