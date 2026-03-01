@extends('layouts.app')

@section('title', 'سجلات الصيانة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-wrench text-warning"></i>
                            سجلات الصيانة
                        </h4>
                    </div>
                    <div>
                        <a href="{{ route('maintenance.schedule') }}" class="btn btn-info me-2">
                            <i class="fas fa-calendar-alt"></i> جدول الصيانة
                        </a>
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> صيانة جديدة
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
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="scheduled" {{ request('type') == 'scheduled' ? 'selected' : '' }}>دورية</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>وقائية
                            </option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>تصحيحية
                            </option>
                            <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>طارئة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>جارية</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- الجدول -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($records->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الآلية</th>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>التكلفة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $record->id }}</td>
                                        <td>
                                            <strong>{{ $record->vehicle->plate_number ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $record->vehicle->model ?? '' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'scheduled' => 'info',
                                                    'preventive' => 'primary',
                                                    'corrective' => 'warning',
                                                    'emergency' => 'danger',
                                                ];
                                            @endphp
                                            <span
                                                class="badge bg-{{ $typeColors[$record->maintenance_type] ?? 'secondary' }}">
                                                {{ $record->type_label }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($record->description, 50) }}</td>
                                        <td>{{ number_format($record->total_cost, 2) }} د.ع</td>
                                        <td>{{ $record->started_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($record->isCompleted())
                                                <span class="badge bg-success">مكتملة</span>
                                            @else
                                                <span class="badge bg-warning">جارية</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('maintenance.show', $record) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (!$record->isCompleted())
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#completeModal{{ $record->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $records->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-wrench fa-4x text-muted mb-3"></i>
                        <h5>لا توجد سجلات صيانة</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
