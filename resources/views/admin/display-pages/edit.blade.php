@extends('layouts.app')

@section('page-title', 'تعديل: ' . ($pageLabels[$pageKey] ?? $pageKey))

@section('content')
    @php
        $typeOptions = []; // لم يعد يُستخدم: كل الكتل عنوان + نص فقط
        $fClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400';
        $lblClass = 'block mb-2 text-sm font-medium text-gray-900 dark:text-white';
    @endphp

    <div class="p-4 bg-white block border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 max-w-5xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $pageLabels[$pageKey] ?? $pageKey }}</h1>
            <a href="{{ route('admin.display-pages.index') }}" class="text-sm text-primary hover:underline">← كل الصفحات</a>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-400">{{ session('success') }}</div>
        @endif

        {{-- فيديوهات — بطاقات بسيطة --}}
        @if (in_array($pageKey, ['landing', 'system_benefits'], true))
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">فيديوهات يوتيوب</h2>
                <p class="text-xs text-gray-500 mb-4">ترتيب العرض من الأعلى للأسفل. اترك القائمة فارغة إن لم ترد عرض فيديو.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($videos as $vid)
                        @php $ytId = $vid->youtubeId(); @endphp
                        <div class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                            <div class="p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                    <div>
                                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded bg-primary/10 text-primary">فيديو #{{ $vid->id }}</span>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mt-1">{{ $vid->title ?: 'بدون عنوان' }}</h3>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <form action="{{ route('admin.display-pages.videos.move', ['publicDisplayVideo' => $vid]) }}" method="post" class="inline">@csrf
                                            <input type="hidden" name="dir" value="up"><button type="submit" class="text-xs px-2 py-1 border rounded dark:border-gray-600" title="أعلى">↑</button>
                                        </form>
                                        <form action="{{ route('admin.display-pages.videos.move', ['publicDisplayVideo' => $vid]) }}" method="post" class="inline">@csrf
                                            <input type="hidden" name="dir" value="down"><button type="submit" class="text-xs px-2 py-1 border rounded dark:border-gray-600" title="أسفل">↓</button>
                                        </form>
                                    </div>
                                </div>
                                @if($ytId)
                                    {{-- نفس طريقة صفحة "/" : iframe مباشر داخل الصفحة --}}
                                    <div class="mb-3 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-black shadow-sm w-full">
                                        <div style="aspect-ratio: 16 / 9; width: 100%;">
                                            <iframe
                                                src="{{ $vid->embed_url }}"
                                                title="{{ $vid->title ?: 'فيديو' }}"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                                allowfullscreen
                                                style="width:100%;height:100%;border:0;display:block;"></iframe>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3 p-3 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-200 text-sm">
                                        ⚠️ تعذر استخراج معرّف الفيديو من الرابط. عدّل الرابط إلى صيغة YouTube صحيحة.
                                    </div>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-300 break-all">{{ \Illuminate\Support\Str::limit($vid->youtube_url, 80) }}</p>
                            </div>
                            <details class="border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50">
                                <summary class="px-4 py-2 text-sm cursor-pointer text-primary hover:underline dark:text-primary">تعديل</summary>
                                <div class="p-4 pt-0 space-y-3">
                                    <form action="{{ route('admin.display-pages.videos.update', ['publicDisplayVideo' => $vid]) }}" method="post" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="{{ $lblClass }}">رابط YouTube</label>
                                            <input type="url" name="youtube_url" value="{{ old('youtube_url', $vid->youtube_url) }}" class="{{ $fClass }}" required>
                                        </div>
                                        <div>
                                            <label class="{{ $lblClass }}">عنوان اختياري</label>
                                            <input type="text" name="title" value="{{ old('title', $vid->title) }}" class="{{ $fClass }}">
                                        </div>
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            <input type="checkbox" name="is_active" value="1" {{ $vid->is_active ? 'checked' : '' }}> مفعّل
                                        </label>
                                        <div>
                                            <button type="submit" class="px-3 py-1.5 bg-primary text-white rounded text-sm">حفظ</button>
                                        </div>
                                    </form>
                                    <form action="{{ route('admin.display-pages.videos.destroy', ['publicDisplayVideo' => $vid]) }}" method="post" class="inline" onsubmit="return confirm('حذف هذا الفيديو؟')">@csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 border border-red-300 text-red-700 rounded text-sm">حذف الفيديو</button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 md:col-span-2">لا توجد فيديوهات بعد.</p>
                    @endforelse
                </div>

                <div class="mt-4 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">إضافة فيديو</h3>
                    <form action="{{ route('admin.display-pages.videos.store', $pageKey) }}" method="post" class="flex flex-col sm:flex-row gap-2 flex-wrap">
                        @csrf
                        <input type="url" name="youtube_url" placeholder="رابط YouTube" class="flex-1 min-w-[200px] {{ $fClass }}" required>
                        <input type="text" name="title" placeholder="عنوان اختياري" class="flex-1 min-w-[140px] {{ $fClass }}">
                        <button type="submit" class="btn btn-info whitespace-nowrap">إضافة فيديو</button>
                    </form>
                </div>
            </section>
        @endif

        {{-- كتل المحتوى — بطاقات: عنوان + نص، والتعديل داخل طي --}}
        <section>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">محتوى الصفحة</h2>
            <p class="text-xs text-gray-500 mb-4">كل كتلة = عنوان (إجباري) + نص (إجباري). الترتيب من الأعلى = العرض في الموقع.</p>

            <div class="space-y-4">
                @foreach ($blocks as $block)
                    @php
                        $typeLabel = 'فقرة';
                        $previewTitle = $block->title ?: 'بدون عنوان';
                        $previewBody = $block->body ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $block->body)), 280) : null;
                    @endphp
                    <article class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                        <div class="p-4 sm:p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-800 dark:text-gray-200">#{{ $block->sort_order }}</span>
                                        <span>{{ $typeLabel }}</span>
                                        @if (! $block->is_active)
                                            <span class="text-amber-600 dark:text-amber-400">(معطّل)</span>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white leading-snug">{{ $previewTitle }}</h3>
                                </div>
                                <div class="flex flex-wrap gap-1 shrink-0">
                                    <form action="{{ route('admin.display-pages.blocks.move', ['publicDisplayBlock' => $block]) }}" method="post" class="inline">@csrf
                                        <input type="hidden" name="dir" value="up"><button type="submit" class="text-xs px-2 py-1.5 border rounded dark:border-gray-600" title="أعلى">↑</button>
                                    </form>
                                    <form action="{{ route('admin.display-pages.blocks.move', ['publicDisplayBlock' => $block]) }}" method="post" class="inline">@csrf
                                        <input type="hidden" name="dir" value="down"><button type="submit" class="text-xs px-2 py-1.5 border rounded dark:border-gray-600" title="أسفل">↓</button>
                                    </form>
                                </div>
                            </div>
                            @if ($previewBody)
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $previewBody }}</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic">لا يوجد نص رئيسي</p>
                            @endif
                        </div>
                        <details class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                            <summary class="px-4 py-2.5 text-sm cursor-pointer text-primary font-medium hover:bg-gray-100/80 dark:hover:bg-gray-800">تعديل (عنوان + نص)</summary>
                            <div class="p-4 sm:p-5 pt-2 space-y-3 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">العنوان والنص مطلوبان.</p>
                                <form action="{{ route('admin.display-pages.blocks.update', ['publicDisplayBlock' => $block]) }}" method="post" class="space-y-3 max-w-3xl">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="{{ $lblClass }}">العنوان (إجباري)</label>
                                        <input type="text" name="title" value="{{ old('title', $block->title) }}" class="{{ $fClass }}" required>
                                    </div>
                                    <div>
                                        <label class="{{ $lblClass }}">النص (إجباري)</label>
                                        <textarea name="body" rows="6" class="{{ $fClass }}" required>{{ old('body', $block->body) }}</textarea>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                        <input type="checkbox" name="is_active" value="1" {{ $block->is_active ? 'checked' : '' }}> مفعّل
                                    </label>
                                    <div class="pt-1">
                                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded text-sm">حفظ التعديلات</button>
                                    </div>
                                </form>
                                <form action="{{ route('admin.display-pages.blocks.destroy', ['publicDisplayBlock' => $block]) }}" method="post" class="inline-block mt-2" onsubmit="return confirm('حذف هذه الكتلة؟')">@csrf @method('DELETE')
                                    <button type="submit" class="px-4 py-2 border border-red-300 text-red-700 rounded text-sm">حذف الكتلة</button>
                                </form>
                            </div>
                        </details>
                    </article>
                @endforeach
            </div>

            {{-- إضافة جديدة --}}
            <div class="mt-8 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm p-5 sm:p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">إضافة كتلة جديدة</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">عنوان + نص فقط (كلاهما إجباري).</p>
                <form action="{{ route('admin.display-pages.blocks.store', $pageKey) }}" method="post" class="space-y-3 max-w-3xl">
                    @csrf
                    <div>
                        <label class="{{ $lblClass }}">العنوان (إجباري)</label>
                        <input type="text" name="title" placeholder="مثال: مقدمة الصفحة، أو اسم القسم" class="{{ $fClass }}" required>
                    </div>
                    <div>
                        <label class="{{ $lblClass }}">النص (إجباري)</label>
                        <textarea name="body" rows="5" placeholder="المحتوى" class="{{ $fClass }}" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-info btn-lg px-5">إضافة الكتلة</button>
                </form>
            </div>
        </section>
    </div>
@endsection
