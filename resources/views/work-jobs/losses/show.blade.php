@extends('layouts.app')

@section('title', 'تفاصيل الخسارة')

@section('content')
    <div class="container-fluid">
        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    خسارة #{{ $loss->id }}
                </h4>
                <span class="badge bg-{{ $loss->status_badge }} fs-6">{{ $loss->status_label }}</span>
            </div>
            <div>
                @if ($loss->status === 'reported')
                    <form action="{{ route('losses.investigate', $loss) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-search"></i> بدء التحقيق
                        </button>
                    </form>
                @endif
                @if ($loss->status === 'investigating')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resolveModal">
                        <i class="fas fa-check"></i> حل المشكلة
                    </button>
                @endif
                @if ($loss->status === 'resolved')
                    <form action="{{ route('losses.close', $loss) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-archive"></i> إغلاق
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- المعلومات الأساسية -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>معلومات الخسارة
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="35%">النوع:</th>
                                <td>
                                    <i class="fas {{ $loss->type_icon }} text-danger me-1"></i>
                                    {{ $loss->type_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>تاريخ الإبلاغ:</th>
                                <td>{{ $loss->reported_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>المبلّغ:</th>
                                <td>{{ $loss->reporter->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>الفرع:</th>
                                <td>{{ $loss->branch->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>أمر العمل:</th>
                                <td>
                                    @if ($loss->job)
                                        <a
                                            href="{{ route('work-jobs.show', $loss->job) }}">{{ $loss->job->job_number }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>الآلية:</th>
                                <td>{{ $loss->vehicle ? $loss->vehicle->plate_number : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-align-right me-2"></i>وصف الخسارة
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $loss->description }}</p>
                    </div>
                </div>

                <!-- الموقع -->
                @if ($loss->location_description || $loss->latitude)
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-map-marker-alt me-2"></i>الموقع
                        </div>
                        <div class="card-body">
                            @if ($loss->location_description)
                                <p>{{ $loss->location_description }}</p>
                            @endif
                            @if ($loss->latitude && $loss->longitude)
                                <small class="text-muted">
                                    الإحداثيات: {{ $loss->latitude }}, {{ $loss->longitude }}
                                </small>
                                <div id="map" style="height: 200px; border-radius: 10px;" class="mt-3"></div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- التكاليف والتحقيق -->
            <div class="col-lg-6">
                <!-- التكاليف -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-money-bill-wave me-2"></i>التكاليف والكميات
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="text-danger">
                                    {{ $loss->quantity_lost ? number_format($loss->quantity_lost, 1) : '-' }}</h4>
                                <small>الكمية المفقودة (م³)</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-warning">{{ number_format($loss->estimated_cost ?? 0, 0) }}</h4>
                                <small>تكلفة تقديرية</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-success">
                                    {{ $loss->actual_cost ? number_format($loss->actual_cost, 0) : '-' }}</h4>
                                <small>تكلفة فعلية</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات التحقيق -->
                @if ($loss->investigated_by || $loss->investigation_notes)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-search me-2"></i>التحقيق
                        </div>
                        <div class="card-body">
                            @if ($loss->investigator)
                                <p>
                                    <strong>المحقق:</strong> {{ $loss->investigator->name }}
                                    @if ($loss->investigated_at)
                                        <br><small
                                            class="text-muted">{{ $loss->investigated_at->format('Y-m-d H:i') }}</small>
                                    @endif
                                </p>
                            @endif
                            @if ($loss->investigation_notes)
                                <p class="mb-0">{{ $loss->investigation_notes }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- معلومات الحل -->
                @if ($loss->resolved_by || $loss->resolution_notes)
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>الحل
                        </div>
                        <div class="card-body">
                            @if ($loss->resolver)
                                <p>
                                    <strong>حل بواسطة:</strong> {{ $loss->resolver->name }}
                                    @if ($loss->resolved_at)
                                        <br><small class="text-muted">{{ $loss->resolved_at->format('Y-m-d H:i') }}</small>
                                    @endif
                                </p>
                            @endif
                            @if ($loss->resolution_notes)
                                <p class="mb-0">{{ $loss->resolution_notes }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- المرفقات -->
                @if ($loss->attachments && count($loss->attachments) > 0)
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-paperclip me-2"></i>المرفقات
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach ($loss->attachments as $attachment)
                                    <div class="col-4">
                                        @if (Str::endsWith($attachment, ['.jpg', '.png', '.jpeg', '.gif']))
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $attachment) }}" class="img-thumbnail">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank"
                                                class="btn btn-outline-secondary w-100">
                                                <i class="fas fa-file"></i> ملف
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- زر العودة -->
        <div class="mt-3">
            <a href="{{ route('losses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    <!-- Modal حل المشكلة -->
    <div class="modal fade" id="resolveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('losses.resolve', $loss) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">حل المشكلة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">التكلفة الفعلية</label>
                            <input type="number" name="actual_cost" class="form-control" step="0.01"
                                value="{{ $loss->estimated_cost }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات الحل <span class="text-danger">*</span></label>
                            <textarea name="resolution_notes" class="form-control" rows="4" required minlength="10"
                                placeholder="اشرح كيف تم حل المشكلة..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد الحل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($loss->latitude && $loss->longitude)
        @push('scripts')
            <script>
                var map = L.map('map').setView([{{ $loss->latitude }}, {{ $loss->longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([{{ $loss->latitude }}, {{ $loss->longitude }}])
                    .addTo(map)
                    .bindPopup('موقع الخسارة').openPopup();
            </script>
        @endpush
    @endif
@endsection
