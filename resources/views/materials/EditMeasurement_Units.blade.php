@extends('layouts.app')

@section('page-title', ' تحديث تفاصيل وحده القياس : '.$EditMeasurement_Units->name)

@section('content')
 
                        {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده--}}   
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                            <div class="panel h-full w-full">
                                <div class="mb-5 flex items-center justify-between">
                                    <h5 class="text-lg font-semibold dark:text-white-light">تحديث تفاصيل وحده القياس : {{$EditMeasurement_Units->name}}</h5>
                                </div>
                              
                                {!! Form::open([
                                    'route' => ['materials.update', $EditMeasurement_Units->id ],
                                    'method' => 'PUT',
                                    'autocomplete' => 'off',
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <!-- اسم المادة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم وحدة القياس <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="name" id="name" placeholder="أدخل اسم وحدة القياس"
                                            value="{{ $EditMeasurement_Units->name }}" class="form-input" required>
                                        @error('name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">رمز وحدة القياس<span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="code" id="code" readonly placeholder="أدخل  رمز وحدة القياس"
                                            value="{{ $EditMeasurement_Units->code}}" class="form-input" required>
                                        @error('code')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                 

                                  



                                    <!-- ملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" id="note" rows="2" placeholder="أدخل أي ملاحظات..." class="form-input">{{ $EditMeasurement_Units->note }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                                         <button type="submit" style="width: 350px" name="active" value="updatemeasurement_units"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>تحديث تفاصيل</span>
                                        </button>

                                        <button type="reset"
                                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i>
                                            <span>إلغاء</span>
                                        </button>

                                       
                                    </div>

                                </div>

                                {!! Form::close() !!}
                            
                                
                            </div>

                        </div>
@endsection
