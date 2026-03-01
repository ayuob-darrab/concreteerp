@extends('layouts.app')

@section('page-title', 'الشحنات 🚛')

@section('content')
    <div class="panel mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">
                <span class="text-2xl">🚛</span> إدارة الشحنات
            </h3>
            <div class="flex items-center gap-2">
                <span class="badge bg-info/20 text-info px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $shipments->total() }} شحنة
                </span>
            </div>
        </div>

        @if ($shipments->count() > 0)
            <div class="table-responsive">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>أمر العمل</th>
                            <th>الخلاطة</th>
                            <th>السائق</th>
                            <th>الكمية</th>
                            <th>وقت الانطلاق</th>
                            <th>وقت الوصول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shipments as $shipment)
                            <tr>
                                <td>{{ $shipment->shipment_number }}</td>
                                <td>
                                    <a href="/ConcreteERP/companyBranch/workJob/{{ $shipment->job_id }}/view"
                                        class="text-primary hover:underline">
                                        {{ $shipment->job->job_number ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $shipment->mixer->car_number ?? '-' }}</td>
                                <td>{{ $shipment->mixerDriver->fullname ?? '-' }}</td>
                                <td>
                                    <span class="font-semibold">{{ $shipment->planned_quantity }} م³</span>
                                    @if ($shipment->actual_quantity)
                                        <br>
                                        <span class="text-xs text-success">(فعلي: {{ $shipment->actual_quantity }}
                                            م³)</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $shipment->departure_time ? \Carbon\Carbon::parse($shipment->departure_time)->format('H:i') : '-' }}
                                </td>
                                <td>
                                    {{ $shipment->arrival_time ? \Carbon\Carbon::parse($shipment->arrival_time)->format('H:i') : '-' }}
                                </td>
                                <td>
                                    @switch($shipment->status)
                                        @case('planned')
                                            <span class="badge bg-secondary">مخطط</span>
                                        @break

                                        @case('preparing')
                                            <span class="badge bg-info">جاري التحضير</span>
                                        @break

                                        @case('departed')
                                            <span class="badge bg-warning">انطلق</span>
                                        @break

                                        @case('arrived')
                                            <span class="badge bg-primary">وصل</span>
                                        @break

                                        @case('working')
                                            <span class="badge bg-info animate-pulse">يعمل</span>
                                        @break

                                        @case('completed')
                                            <span class="badge bg-success">أكمل</span>
                                        @break

                                        @case('returned')
                                            <span class="badge bg-success">عاد</span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge bg-danger">ملغي</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">{{ $shipment->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="/ConcreteERP/companyBranch/shipment/{{ $shipment->id }}/view"
                                            class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        @if (in_array($shipment->status, ['planned', 'preparing']))
                                            <button type="button" onclick="departShipment({{ $shipment->id }})"
                                                class="btn btn-sm btn-success" title="انطلاق">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ترقيم الصفحات --}}
            <div class="mt-4">
                {{ $shipments->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <p class="text-gray-500 text-lg">لا توجد شحنات</p>
            </div>
        @endif
    </div>

    <script>
        function departShipment(id) {
            if (confirm('هل تريد تأكيد انطلاق هذه الشحنة؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/ConcreteERP/companyBranch/shipment/${id}/depart`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
