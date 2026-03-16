@extends('layouts.app')

@section('page-title', 'لوحة التحكم الرئيسية')

@section('content')
 
                        {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده--}}   
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                            <div class="panel h-full w-full">
                              
                                  <div class="p-6">

                                        {!! Form::open([
                            'route' => ['companies.update', $company->id],
                            'method' => 'PUT',
                            'autocomplete' => 'off',
                            'files' => true,
                        ]) !!}


                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <!-- اسم الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشركة <span class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="name" placeholder="أدخل اسم الشركة"
                                            value="{{ $company->name }}" class="form-input" required>
                                        @error('name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم مدير الشركة <span class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="managername" placeholder="أدخل اسم مدير الشركة"
                                            value="{{ $company->managername }}" class="form-input" required>
                                        @error('managername')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- المحافظة -->
                                    <div class="space-y-3">
                                        <label for="city_id" class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">المحافظة <span class="text-danger">*</span></span>
                                        </label>
                                        <select name="city_id" id="city_id" class="form-select" required>
                                            <option value="" disabled selected>اختر المحافظة</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" {{ $company->city_id ==  $city->id ? 'selected' : '' }}>{{ $city->name_ar }}</option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الهاتف -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الهاتف</span>
                                        </label>
                                        <input type="text" name="phone" placeholder="أدخل رقم الهاتف"
                                            value="{{ $company->phone }}" class="form-input" maxlength="11" minlength="8">
                                        @error('phone')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- البريد الإلكتروني -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">البريد الإلكتروني</span>
                                        </label>
                                        <input type="email" name="email" placeholder="example@domain.com"
                                            value="{{ $company->email }}" class="form-input">
                                        @error('email')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- عنوان الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">العنوان</span>
                                        </label>
                                        <input type="text" name="address" placeholder="أدخل عنوان الشركة"
                                            value="{{ $company->address }}" class="form-input">
                                        @error('address')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- شعار الشركة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">شعار الشركة</span>
                                        </label>
                                        <input type="file" name="logo" class="form-input" accept="image/*">
                                        @error('logo')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                       <!-- الحالة -->
                                    <div class="space-y-3">
                                        <label for="is_active" class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الحالة <span class="text-danger">*</span></span>
                                        </label>
                                        <select name="is_active" id="is_active" class="form-select" required>
                                            <option value="" disabled selected>اختر الحالة</option>
                                                <option value="1" {{ $company->is_active ==  '1' ? 'selected' : '' }}>تفعيل</option>
                                                <option value="0" {{ $company->is_active ==  '0' ? 'selected' : '' }}>تعطيل</option>

                                        </select>
                                        @error('is_active')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="flex gap-4 lg:col-span-2 justify-center mt-4">
                                        <button type="submit" name="active" value="edit_informationCompany"
                                            class="btn btn-primary flex items-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>  تحديث المعلومات
                                        </button>
                                        <button type="reset"
                                            class="btn btn-outline-secondary flex items-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i> إلغاء
                                        </button>
                                    </div>

                                </div>
                                {!! Form::close() !!}
                            </div>
                            
                            </div>

                        </div>
@endsection
