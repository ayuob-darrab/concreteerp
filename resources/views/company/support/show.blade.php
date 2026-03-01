@extends('layouts.app')

@section('page-title', 'تفاصيل التذكرة')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('support.index') }}"
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-primary hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $ticket->subject }}</h2>
                    @php
                        $statusColors = [
                            'open' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            'pending_response' =>
                                'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                            'resolved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                        ];
                        $priorityColors = [
                            'low' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'medium' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                            'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        ];
                    @endphp
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $ticket->status_name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $priorityColors[$ticket->priority] ?? '' }}">
                        {{ $ticket->priority_name }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    <span class="font-mono">{{ $ticket->ticket_number }}</span> • {{ $ticket->category_name }}
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- المحادثة --}}
            <div class="lg:col-span-2">
                <div class="panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-lg">💬 المحادثة</h3>
                        <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                            {{ $ticket->replies->count() + 1 }} رسالة
                        </span>
                    </div>

                    <div class="space-y-4 mb-6 max-h-[500px] overflow-y-auto pr-2">
                        {{-- الوصف الأصلي --}}
                        <div
                            class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 rounded-xl p-4 border border-blue-200/50 dark:border-blue-800/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                    {{ mb_substr($ticket->user->fullname ?? 'U', 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-gray-800 dark:text-white">
                                        {{ $ticket->user->fullname ?? 'أنت' }}</p>
                                    <p class="text-xs text-gray-500">{{ $ticket->created_at->format('Y/m/d - h:i A') }}</p>
                                </div>
                                <span
                                    class="text-xs text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/50 px-2 py-1 rounded-full">صاحب
                                    التذكرة</span>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                                {{ $ticket->description }}</div>

                            @if ($ticket->attachments && count($ticket->attachments) > 0)
                                <div class="mt-4 pt-3 border-t border-blue-200/50 dark:border-blue-700/50">
                                    <p class="text-xs text-gray-500 mb-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                            </path>
                                        </svg>
                                        المرفقات ({{ count($ticket->attachments) }})
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($ticket->attachments as $attachment)
                                            @php
                                                $ext = pathinfo($attachment['path'], PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($ext), [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'webp',
                                                ]);
                                            @endphp
                                            @if ($isImage)
                                                <a href="{{ asset($attachment['path']) }}" target="_blank"
                                                    class="block group">
                                                    <img src="{{ asset($attachment['path']) }}"
                                                        alt="{{ $attachment['name'] }}"
                                                        class="w-16 h-16 object-cover rounded-lg border-2 border-white shadow-sm group-hover:border-blue-400 transition-all">
                                                </a>
                                            @else
                                                <a href="{{ asset($attachment['path']) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-700 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-50 shadow-sm border border-gray-200 dark:border-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    {{ $attachment['name'] }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- الردود --}}
                        @foreach ($ticket->replies as $reply)
                            @if (!$reply->is_internal)
                                <div
                                    class="{{ $reply->isFromSupport() ? 'bg-gradient-to-br from-green-50 to-green-100/50 dark:from-green-900/20 dark:to-green-800/10 border-green-200/50 dark:border-green-800/30' : 'bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700' }} rounded-xl p-4 border">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div
                                            class="w-10 h-10 {{ $reply->isFromSupport() ? 'bg-green-500' : 'bg-blue-500' }} rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                            @if ($reply->isFromSupport())
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                                    </path>
                                                </svg>
                                            @else
                                                {{ mb_substr($reply->user_name ?? 'U', 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-medium text-sm text-gray-800 dark:text-white">
                                                {{ $reply->isFromSupport() ? 'فريق الدعم الفني' : $reply->user_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $reply->created_at->format('Y/m/d - h:i A') }}</p>
                                        </div>
                                        @if ($reply->isFromSupport())
                                            <span
                                                class="text-xs text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/50 px-2 py-1 rounded-full">الدعم
                                                الفني</span>
                                        @endif
                                    </div>
                                    <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                                        {{ $reply->message }}</div>

                                    @if ($reply->attachments && count($reply->attachments) > 0)
                                        <div
                                            class="mt-4 pt-3 border-t {{ $reply->isFromSupport() ? 'border-green-200/50 dark:border-green-700/50' : 'border-gray-200 dark:border-gray-700' }}">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($reply->attachments as $attachment)
                                                    @php
                                                        $ext = pathinfo($attachment['path'], PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($ext), [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                            'webp',
                                                        ]);
                                                    @endphp
                                                    @if ($isImage)
                                                        <a href="{{ asset($attachment['path']) }}" target="_blank"
                                                            class="block group">
                                                            <img src="{{ asset($attachment['path']) }}"
                                                                alt="{{ $attachment['name'] }}"
                                                                class="w-16 h-16 object-cover rounded-lg border-2 border-white shadow-sm group-hover:border-green-400 transition-all">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($attachment['path']) }}" target="_blank"
                                                            class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-700 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-50 shadow-sm border border-gray-200 dark:border-gray-600">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                            {{ $attachment['name'] }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- نموذج الرد --}}
                    @if (!in_array($ticket->status, ['closed']))
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-sm mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                    </path>
                                </svg>
                                إضافة رد
                            </h4>
                            <form action="{{ route('support.reply', $ticket->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="message" rows="3" class="form-textarea w-full" placeholder="اكتب ردك هنا..." required></textarea>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <label
                                        class="cursor-pointer inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition-colors bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg">
                                        <input type="file" name="attachments[]" multiple class="hidden"
                                            accept="image/*,.pdf,.doc,.docx">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                            </path>
                                        </svg>
                                        إرفاق ملفات
                                    </label>
                                    <button type="submit" class="btn btn-primary">
                                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        إرسال الرد
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="border-t pt-4">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                <p class="text-sm text-gray-500">تم إغلاق هذه التذكرة ولا يمكن إضافة ردود</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- معلومات التذكرة --}}
            <div class="lg:col-span-1">
                <div class="panel sticky top-4">
                    <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        معلومات التذكرة
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500">رقم التذكرة</span>
                            <span class="font-mono font-medium text-sm">{{ $ticket->ticket_number }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500">الحالة</span>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $ticket->status_name }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500">الأولوية</span>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $priorityColors[$ticket->priority] ?? '' }}">
                                {{ $ticket->priority_name }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500">التصنيف</span>
                            <span class="text-sm font-medium">{{ $ticket->category_name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500">تاريخ الإنشاء</span>
                            <span class="text-sm">{{ $ticket->created_at->format('Y/m/d') }}</span>
                        </div>
                        @if ($ticket->resolved_at)
                            <div
                                class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-xs text-gray-500">تاريخ الحل</span>
                                <span class="text-sm text-green-600">{{ $ticket->resolved_at->format('Y/m/d') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- الإجراءات --}}
                    <div class="border-t mt-4 pt-4 space-y-2">
                        @if ($ticket->status === 'resolved')
                            <button type="button" onclick="showRatingModal()" class="btn btn-success w-full">
                                <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                تأكيد الحل وإغلاق التذكرة
                            </button>
                            <form action="{{ route('support.reopen', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-full">
                                    <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    إعادة فتح التذكرة
                                </button>
                            </form>
                        @elseif($ticket->status === 'closed')
                            @if ($ticket->rating)
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <p class="text-xs text-gray-500 mb-2">تقييمك للدعم:</p>
                                    <div class="flex justify-center gap-1 mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $ticket->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                        @endfor
                                    </div>
                                    @if ($ticket->feedback)
                                        <p class="text-xs text-gray-500 italic">"{{ $ticket->feedback }}"</p>
                                    @endif
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">تم إغلاق هذه التذكرة</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal التقييم --}}
    <div id="ratingModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <form action="{{ route('support.close', $ticket->id) }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div
                            class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">قيّم تجربتك</h3>
                        <p class="text-sm text-gray-500 mt-1">كيف كانت خدمة الدعم الفني؟</p>
                    </div>

                    <div class="flex justify-center gap-2 mb-6" id="starRating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})"
                                class="star-btn text-gray-300 hover:text-yellow-400 transition-all transform hover:scale-110">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="5">

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">ملاحظات إضافية
                            (اختياري)</label>
                        <textarea name="feedback" rows="3" class="form-textarea w-full"
                            placeholder="شاركنا رأيك لنتمكن من تحسين خدماتنا..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl">
                    <button type="button" onclick="closeRatingModal()"
                        class="btn btn-outline-secondary flex-1">إلغاء</button>
                    <button type="submit" class="btn btn-success flex-1">
                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        إرسال التقييم
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRatingModal() {
            document.getElementById('ratingModal').classList.remove('hidden');
            document.getElementById('ratingModal').classList.add('flex');
            setRating(5);
        }

        function closeRatingModal() {
            document.getElementById('ratingModal').classList.add('hidden');
            document.getElementById('ratingModal').classList.remove('flex');
        }

        function setRating(rating) {
            document.getElementById('ratingInput').value = rating;
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.add('text-gray-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        }

        // Close modal on backdrop click
        document.getElementById('ratingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRatingModal();
            }
        });
    </script>
@endsection
