@extends('layouts.app')

@section('title', 'إعدادات الإشعارات')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                <i class="fas fa-cog me-2"></i>
                إعدادات الإشعارات
            </h4>
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>

        <form action="{{ route('notifications.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach ($templates as $groupKey => $group)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $group['label'] }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>نوع الإشعار</th>
                                        <th class="text-center" style="width: 100px;">
                                            <i class="fas fa-mobile-alt me-1"></i> التطبيق
                                        </th>
                                        <th class="text-center" style="width: 100px;">
                                            <i class="fas fa-sms me-1"></i> SMS
                                        </th>
                                        <th class="text-center" style="width: 100px;">
                                            <i class="fab fa-whatsapp me-1"></i> واتساب
                                        </th>
                                        <th class="text-center" style="width: 100px;">
                                            <i class="fas fa-envelope me-1"></i> بريد
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['templates'] as $template)
                                        @php
                                            $setting = $settings[$template->type] ?? null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $template->title_ar }}</strong>
                                                <br><small
                                                    class="text-muted">{{ Str::limit($template->body_ar, 50) }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="settings[{{ $template->type }}][]" value="app"
                                                        {{ $setting['app_enabled'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="settings[{{ $template->type }}][]" value="sms"
                                                        {{ $setting['sms_enabled'] ?? false ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="settings[{{ $template->type }}][]" value="whatsapp"
                                                        {{ $setting['whatsapp_enabled'] ?? false ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="settings[{{ $template->type }}][]" value="email"
                                                        {{ $setting['email_enabled'] ?? false ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
@endsection
