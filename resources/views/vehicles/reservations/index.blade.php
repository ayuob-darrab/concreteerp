@extends('layouts.app')

@section('title', 'حجوزات الآليات')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-calendar-check text-primary"></i>
                            حجوزات الآليات
                        </h4>
                    </div>
                    <div>
                        <a href="{{ route('vehicle-reservations.calendar') }}" class="btn btn-info me-2">
                            <i class="fas fa-calendar"></i> التقويم
                        </a>
                        <a href="{{ route('vehicle-reservations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> حجز جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الآلية</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>قيد الاستخدام
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-filter"></i> تصفية
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- الجدول -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($reservations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الآلية</th>
                                    <th>الفترة</th>
                                    <th>السائق</th>
                                    <th>الغرض</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservations as $reservation)
                                    <tr>
                                        <td>{{ $reservation->id }}</td>
                                        <td>
                                            <strong>{{ $reservation->vehicle->plate_number ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $reservation->vehicle->model ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $reservation->reserved_from->format('Y-m-d H:i') }}<br>
                                            <small class="text-muted">إلى
                                                {{ $reservation->reserved_to->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>{{ $reservation->driver->name ?? '-' }}</td>
                                        <td>{{ Str::limit($reservation->purpose, 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $reservation->status_color }}">
                                                {{ $reservation->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($reservation->canBeConfirmed())
                                                <form action="{{ route('vehicle-reservations.confirm', $reservation) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="تأكيد">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($reservation->status === 'confirmed')
                                                <form action="{{ route('vehicle-reservations.start', $reservation) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        title="بدء الاستخدام">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($reservation->status === 'in_use')
                                                <form action="{{ route('vehicle-reservations.complete', $reservation) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="إكمال">
                                                        <i class="fas fa-flag-checkered"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($reservation->canBeCancelled())
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#cancelModal{{ $reservation->id }}" title="إلغاء">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $reservations->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5>لا توجد حجوزات</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modals للإلغاء -->
    @foreach ($reservations as $reservation)
        @if ($reservation->canBeCancelled())
            <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('vehicle-reservations.cancel', $reservation) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">إلغاء الحجز</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">سبب الإلغاء</label>
                                    <textarea name="reason" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
