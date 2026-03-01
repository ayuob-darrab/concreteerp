@extends('layouts.app')

@section('page-title', 'إدارة الفئات السعرية')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="panel mt-6">
            <!-- العنوان والأزرار -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">
                    <svg class="inline-block w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    إدارة الفئات السعرية للخرسانة
                </h5>

                <button type="button" class="btn btn-primary" x-data x-on:click="$dispatch('open-modal')">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة فئة جديدة
                </button>
            </div>

            <!-- رسائل النجاح والخطأ -->
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- جدول الفئات -->
            <div class="table-responsive">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>اسم الفئة</th>
                            <th>الوصف</th>
                            <th class="text-center">الترتيب</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">عدد الأسعار</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="font-semibold">{{ $category->name }}</span>
                                </td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td class="text-center">{{ $category->sort_order }}</td>
                                <td class="text-center">
                                    @if ($category->is_active)
                                        <span class="badge bg-success">نشطة</span>
                                    @else
                                        <span class="badge bg-danger">معطلة</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $category->mixPrices()->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- زر التعديل -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" x-data
                                            x-on:click="$dispatch('edit-category', {{ json_encode($category) }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        <!-- زر تغيير الحالة -->
                                        <form action="{{ route('pricing-categories.toggle', $category->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $category->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $category->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                @if ($category->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- زر الحذف -->
                                        @if ($category->mixPrices()->count() == 0)
                                            <form action="{{ route('pricing-categories.destroy', $category->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    لا توجد فئات سعرية. قم بإضافة فئة جديدة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- شرح الفئات -->
        <div class="panel mt-6">
            <h5 class="font-semibold text-lg mb-4 dark:text-white-light">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                كيف تعمل الفئات السعرية؟
            </h5>
            <div class="text-gray-600 dark:text-gray-400 space-y-2">
                <p>• <strong>الفئات السعرية</strong> تتيح لك تحديد أسعار مختلفة لنفس نوع الخرسانة.</p>
                <p>• مثال: C20 يمكن أن يكون له سعر في <strong>الفئة الأولى</strong> = 150,000 دينار، وسعر في <strong>الفئة
                        الثانية</strong> = 200,000 دينار.</p>
                <p>• الفرق بين الفئات يمكن أن يكون بسبب: المواد الكيميائية الإضافية، جودة المواد، خدمات إضافية.</p>
                <p>• <strong>السوبر أدمن</strong> يضيف الفئات العامة، و<strong>كل شركة</strong> تحدد أسعارها الخاصة.</p>
            </div>
        </div>
    </div>

    <!-- Modal إضافة/تعديل فئة -->
    <div x-data="categoryModal()" x-show="isOpen" x-cloak @open-modal.window="openAdd()"
        @edit-category.window="openEdit($event.detail)"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto p-4">

        <div x-show="isOpen" x-transition @click.outside="close()"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <h5 class="text-lg font-semibold" x-text="isEdit ? 'تعديل الفئة السعرية' : 'إضافة فئة سعرية جديدة'"></h5>
                <button @click="close()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <form
                :action="isEdit ? '{{ url('pricing-categories') }}/' + category.id : '{{ route('pricing-categories.store') }}'"
                method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-4 space-y-4">
                    <div>
                        <label class="block mb-1 font-medium">اسم الفئة <span class="text-danger">*</span></label>
                        <input type="text" name="name" x-model="category.name" class="form-input w-full"
                            placeholder="مثال: الفئة الأولى" required>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">الوصف</label>
                        <textarea name="description" x-model="category.description" rows="3" class="form-input w-full"
                            placeholder="وصف اختياري للفئة..."></textarea>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">الترتيب</label>
                        <input type="number" name="sort_order" x-model="category.sort_order" min="0"
                            class="form-input w-full" placeholder="0">
                        <small class="text-gray-500">الأرقام الأصغر تظهر أولاً</small>
                    </div>

                    <template x-if="isEdit">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" x-model="category.is_active"
                                value="1" class="form-checkbox">
                            <label for="is_active">الفئة نشطة</label>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-2 p-4 border-t dark:border-gray-700">
                    <button type="button" @click="close()" class="btn btn-outline-secondary">إلغاء</button>
                    <button type="submit" class="btn btn-primary" x-text="isEdit ? 'تحديث' : 'إضافة'"></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('categoryModal', () => ({
                isOpen: false,
                isEdit: false,
                category: {
                    id: null,
                    name: '',
                    description: '',
                    sort_order: 0,
                    is_active: true
                },

                openAdd() {
                    this.isEdit = false;
                    this.category = {
                        id: null,
                        name: '',
                        description: '',
                        sort_order: 0,
                        is_active: true
                    };
                    this.isOpen = true;
                },

                openEdit(data) {
                    this.isEdit = true;
                    this.category = {
                        ...data
                    };
                    this.isOpen = true;
                },

                close() {
                    this.isOpen = false;
                }
            }));
        });
    </script>
@endsection
