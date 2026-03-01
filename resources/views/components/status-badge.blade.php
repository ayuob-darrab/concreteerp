{{-- Status Badge Component --}}
@props([
    'status' => '',
    'type' => 'default', // order, work, advance, vehicle, employee
])

@php
    $configs = [
        'order' => [
            'pending' => ['label' => 'قيد الانتظار', 'color' => 'warning'],
            'approved' => ['label' => 'معتمد', 'color' => 'info'],
            'in_progress' => ['label' => 'جاري التنفيذ', 'color' => 'primary'],
            'completed' => ['label' => 'مكتمل', 'color' => 'success'],
            'cancelled' => ['label' => 'ملغي', 'color' => 'danger'],
            'rejected' => ['label' => 'مرفوض', 'color' => 'danger'],
        ],
        'work' => [
            'pending' => ['label' => 'قيد الانتظار', 'color' => 'warning'],
            'assigned' => ['label' => 'تم التعيين', 'color' => 'info'],
            'in_transit' => ['label' => 'في الطريق', 'color' => 'primary'],
            'arrived' => ['label' => 'وصل', 'color' => 'info'],
            'pouring' => ['label' => 'جاري الصب', 'color' => 'primary'],
            'completed' => ['label' => 'مكتمل', 'color' => 'success'],
            'cancelled' => ['label' => 'ملغي', 'color' => 'danger'],
        ],
        'advance' => [
            'pending' => ['label' => 'قيد الموافقة', 'color' => 'warning'],
            'active' => ['label' => 'نشطة', 'color' => 'primary'],
            'paid' => ['label' => 'مسددة', 'color' => 'success'],
            'cancelled' => ['label' => 'ملغاة', 'color' => 'danger'],
        ],
        'vehicle' => [
            'active' => ['label' => 'نشطة', 'color' => 'success'],
            'maintenance' => ['label' => 'في الصيانة', 'color' => 'warning'],
            'inactive' => ['label' => 'متوقفة', 'color' => 'danger'],
            'reserved' => ['label' => 'محجوزة', 'color' => 'info'],
        ],
        'employee' => [
            'active' => ['label' => 'نشط', 'color' => 'success'],
            'inactive' => ['label' => 'غير نشط', 'color' => 'secondary'],
            'on_leave' => ['label' => 'في إجازة', 'color' => 'info'],
            'terminated' => ['label' => 'منتهي', 'color' => 'danger'],
        ],
        'default' => [
            'active' => ['label' => 'نشط', 'color' => 'success'],
            'inactive' => ['label' => 'غير نشط', 'color' => 'secondary'],
            'pending' => ['label' => 'معلق', 'color' => 'warning'],
            'completed' => ['label' => 'مكتمل', 'color' => 'success'],
            'cancelled' => ['label' => 'ملغي', 'color' => 'danger'],
        ],
    ];

    $config =
        $configs[$type][$status] ?? ($configs['default'][$status] ?? ['label' => $status, 'color' => 'secondary']);
@endphp

<span class="badge bg-{{ $config['color'] }}">
    {{ $config['label'] }}
</span>
