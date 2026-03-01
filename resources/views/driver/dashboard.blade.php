@extends('layouts.app')

@section('page-title', 'لوحة السائق')

@section('content')
    <div class="mt-6 space-y-6">
        <!-- ترحيب -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                    مرحباً {{ auth()->user()->name }}
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->locale('ar')->format('l j F Y') }}
                </p>
            </div>
        </div>

        <!-- الشحنة الحالية -->
        @if ($currentShipment)
            <div class="panel border border-primary/30 shadow-sm">
                <div
                    class="flex items-center justify-between border-b border-primary/20 pb-3 mb-4 text-primary dark:text-primary">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-truck text-lg"></i>
                        <span class="font-semibold">الشحنة الحالية</span>
                    </div>
                    <span
                        class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                        {{ $currentShipment->status_label }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">رقم الأمر</div>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ $currentShipment->job->job_number }}
                        </div>
                    </div>
                    <div class="md:text-left text-right">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الكمية</div>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ number_format($currentShipment->planned_quantity, 1) }} م³
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">العميل</div>
                    <p class="mb-0 font-semibold text-gray-800 dark:text-gray-100">
                        {{ $currentShipment->job->customer_name }}
                    </p>
                </div>

                <div class="mb-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الوجهة</div>
                    <p class="mb-0 text-gray-800 dark:text-gray-100">
                        {{ $currentShipment->job->location_address }}
                    </p>
                    @if ($currentShipment->job->location_map_url)
                        <a href="{{ $currentShipment->job->location_map_url }}" target="_blank"
                            class="btn btn-sm btn-outline-info mt-2 inline-flex items-center gap-1">
                            <i class="fas fa-map"></i>
                            <span>فتح الخريطة</span>
                        </a>
                    @endif
                </div>

                <!-- أزرار الإجراءات -->
                <form action="{{ route('driver.update-status', $currentShipment) }}" method="POST" id="statusForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if ($currentShipment->can_depart)
                            <button type="submit" name="action" value="depart" class="btn btn-primary w-full">
                                <i class="fas fa-truck me-2"></i> انطلاق
                            </button>
                        @endif

                        @if ($currentShipment->can_arrive)
                            <button type="submit" name="action" value="arrive" class="btn btn-info w-full">
                                <i class="fas fa-map-marker-alt me-2"></i> وصول
                            </button>
                        @endif

                        @if ($currentShipment->can_start_work)
                            <button type="submit" name="action" value="start_work" class="btn btn-warning w-full">
                                <i class="fas fa-play me-2"></i> بدء العمل
                            </button>
                        @endif

                        @if ($currentShipment->can_end_work)
                            <button type="button" class="btn btn-success w-full" data-bs-toggle="modal"
                                data-bs-target="#endWorkModal">
                                <i class="fas fa-stop me-2"></i> انتهاء العمل
                            </button>
                        @endif

                        @if ($currentShipment->can_return)
                            <button type="submit" name="action" value="return" class="btn btn-dark w-full">
                                <i class="fas fa-home me-2"></i> عودة للمقر
                            </button>
                        @endif
                    </div>
                </form>

                <!-- زر تقرير مشكلة -->
                <button type="button" class="btn btn-outline-danger w-full mt-4" data-bs-toggle="modal"
                    data-bs-target="#issueModal">
                    <i class="fas fa-exclamation-triangle me-1"></i> الإبلاغ عن مشكلة
                </button>
            </div>
        @else
            <div class="panel text-center py-10">
                <div class="mb-4 text-4xl text-gray-400">
                    <i class="fas fa-coffee"></i>
                </div>
                <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-100">لا توجد شحنة حالية</h5>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">انتظر تعيينك لشحنة جديدة</p>
            </div>
        @endif

        <!-- إحصائيات اليوم -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="panel text-center">
                <div class="text-2xl font-bold text-primary mb-1">
                    {{ $todayStats['total'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">شحنات اليوم</div>
            </div>
            <div class="panel text-center">
                <div class="text-2xl font-bold text-emerald-500 mb-1">
                    {{ $todayStats['completed'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">مكتملة</div>
            </div>
            <div class="panel text-center">
                <div class="text-2xl font-bold text-sky-500 mb-1">
                    {{ number_format($todayStats['total_quantity'], 1) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">م³</div>
            </div>
        </div>

        <!-- شحنات اليوم -->
        @if ($todayShipments->count() > 0)
            <div class="panel">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-list text-gray-500"></i>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">شحنات اليوم</span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($todayShipments as $shipment)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $shipment->job->job_number }} #{{ $shipment->shipment_number }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ number_format($shipment->planned_quantity, 1) }} م³
                                </div>
                            </div>
                            <span
                                class="badge bg-{{ $shipment->status_badge }} text-xs px-3 py-1 rounded-full whitespace-nowrap">
                                {{ $shipment->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- رابط السجل -->
        <div class="text-center">
            <a href="{{ route('driver.history') }}" class="btn btn-outline-secondary inline-flex items-center gap-2">
                <i class="fas fa-history"></i>
                <span>عرض السجل الكامل</span>
            </a>
        </div>
    </div>

    @if ($currentShipment)
        <!-- Modal انتهاء العمل -->
        <div class="modal fade" id="endWorkModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('driver.update-status', $currentShipment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="end_work">
                        <input type="hidden" name="latitude" class="latitude-input">
                        <input type="hidden" name="longitude" class="longitude-input">
                        <div class="modal-header">
                            <h5 class="modal-title">تسجيل انتهاء العمل</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">الكمية الفعلية (م³)</label>
                                <input type="number" name="actual_quantity"
                                    class="form-control form-control-lg text-center"
                                    value="{{ $currentShipment->planned_quantity }}" step="0.1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات (اختياري)</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success">تأكيد الانتهاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal تقرير مشكلة -->
        <div class="modal fade" id="issueModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('driver.report-issue', $currentShipment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="latitude" class="latitude-input">
                        <input type="hidden" name="longitude" class="longitude-input">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">الإبلاغ عن مشكلة</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">وصف المشكلة</label>
                                <textarea name="description" class="form-control" rows="4" required minlength="10"
                                    placeholder="اشرح المشكلة بالتفصيل..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">إرسال البلاغ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // الحصول على الموقع الحالي
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;

                        // تحديث كل حقول الموقع في النماذج
                        document.querySelectorAll('.latitude-input').forEach(el => el.value = position.coords.latitude);
                        document.querySelectorAll('.longitude-input').forEach(el => el.value = position.coords
                            .longitude);
                    });
                }
            }

            // الحصول على الموقع عند تحميل الصفحة
            getLocation();

            // تحديث الموقع كل 30 ثانية إذا كانت هناك شحنة نشطة
            @if ($currentShipment && $currentShipment->is_active)
                setInterval(function() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            fetch('/driver/location', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    shipment_id: {{ $currentShipment->id }},
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    accuracy: position.coords.accuracy
                                })
                            });
                        });
                    }
                }, 30000);
            @endif
        </script>
    @endpush
@endsection
