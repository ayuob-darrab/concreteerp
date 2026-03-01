@extends('layouts.app')

@section('title', 'إعدادات نظام السلف')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="fas fa-cog text-primary"></i>
                إعدادات نظام السلف
            </h3>
            <a href="{{ route('advances.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('advances.settings.save') }}" method="POST">
            @csrf

            <div class="row">
                <!-- الحدود القصوى -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> الحدود القصوى للسلف</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">الحد الأقصى لسلف الموظفين</label>
                                <div class="input-group">
                                    <input type="number" name="max_employee_advance" class="form-control"
                                        value="{{ $settings->max_employee_advance ?? 0 }}" min="0">
                                    <span class="input-group-text">د.ع</span>
                                </div>
                                <small class="text-muted">0 = بدون حد</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الحد الأقصى لسلف المناديب</label>
                                <div class="input-group">
                                    <input type="number" name="max_agent_advance" class="form-control"
                                        value="{{ $settings->max_agent_advance ?? 0 }}" min="0">
                                    <span class="input-group-text">د.ع</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الحد الأقصى لسلف الموردين</label>
                                <div class="input-group">
                                    <input type="number" name="max_supplier_advance" class="form-control"
                                        value="{{ $settings->max_supplier_advance ?? 0 }}" min="0">
                                    <span class="input-group-text">د.ع</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الحد الأقصى لسلف المقاولين</label>
                                <div class="input-group">
                                    <input type="number" name="max_contractor_advance" class="form-control"
                                        value="{{ $settings->max_contractor_advance ?? 0 }}" min="0">
                                    <span class="input-group-text">د.ع</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نسب الاستقطاع الافتراضية -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-percentage me-2"></i> نسب الاستقطاع الافتراضية</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">نسبة استقطاع الموظفين</label>
                                <div class="input-group">
                                    <input type="number" name="default_employee_deduction" class="form-control"
                                        value="{{ $settings->default_employee_deduction ?? 10 }}" min="0"
                                        max="100" step="0.5">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نسبة استقطاع المناديب</label>
                                <div class="input-group">
                                    <input type="number" name="default_agent_deduction" class="form-control"
                                        value="{{ $settings->default_agent_deduction ?? 10 }}" min="0" max="100"
                                        step="0.5">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نسبة استقطاع الموردين</label>
                                <div class="input-group">
                                    <input type="number" name="default_supplier_deduction" class="form-control"
                                        value="{{ $settings->default_supplier_deduction ?? 10 }}" min="0"
                                        max="100" step="0.5">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نسبة استقطاع المقاولين</label>
                                <div class="input-group">
                                    <input type="number" name="default_contractor_deduction" class="form-control"
                                        value="{{ $settings->default_contractor_deduction ?? 10 }}" min="0"
                                        max="100" step="0.5">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإعدادات العامة -->
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i> الإعدادات العامة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="auto_deduction_enabled"
                                            id="auto_deduction_enabled"
                                            {{ $settings->auto_deduction_enabled ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_deduction_enabled">
                                            <strong>تفعيل الاستقطاع التلقائي</strong>
                                            <br><small class="text-muted">استقطاع تلقائي من الرواتب والمستحقات</small>
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="allow_multiple_advances"
                                            id="allow_multiple_advances"
                                            {{ $settings->allow_multiple_advances ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_multiple_advances">
                                            <strong>السماح بسلف متعددة</strong>
                                            <br><small class="text-muted">السماح للمستفيد بأكثر من سلفة نشطة</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="require_approval"
                                            id="require_approval"
                                            {{ $settings->require_approval ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_approval">
                                            <strong>طلب موافقة</strong>
                                            <br><small class="text-muted">تتطلب السلف موافقة المدير قبل التفعيل</small>
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="allow_overpayment"
                                            id="allow_overpayment"
                                            {{ $settings->allow_overpayment ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_overpayment">
                                            <strong>السماح بالدفع الزائد</strong>
                                            <br><small class="text-muted">السماح بتسديد مبلغ أكبر من المتبقي</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
@endsection
