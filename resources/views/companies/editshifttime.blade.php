@extends('layouts.app')

@section('page-title', 'تعديل معلومات الشفت : '.$EditShiftTime->name)

@section('content')
 
                        {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده--}}   
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                            <div class="panel h-full w-full">
                                <div class="mb-5 flex items-center justify-between">
                                    <h5 class="text-lg font-semibold dark:text-white-light">تحديث معلومات شفت العمل : {{$EditShiftTime->name}} </h5>
                                </div>
                               <div class="p-6">
                          

                                             {!! Form::open([
                            'route' => ['companies.shift-times.update', $EditShiftTime->id],
                            'method' => 'PUT',
                            'autocomplete' => 'off',
                            'files' => true,
                        ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="shift_name" id="shift_name" placeholder="أدخل اسم الشفت"
                                            value="{{$EditShiftTime->name }}" class="form-input" required>
                                        @error('shift_name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- بداية الشفت -->
                                    <div class="space-y-3 cursor-pointer"
                                        onclick="document.getElementById('start_time').showPicker()">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">بداية الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="time" name="start_time" id="start_time"
                                            value="{{$EditShiftTime->start_time }}" class="form-input cursor-pointer" required>
                                        @error('start_time')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- نهاية الشفت -->
                                    <div class="space-y-3 cursor-pointer"
                                        onclick="document.getElementById('end_time').showPicker()">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">نهاية الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="time" name="end_time" id="end_time" value="{{ $EditShiftTime->end_time }}"
                                            class="form-input cursor-pointer" required>
                                        @error('end_time')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <!-- ملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" id="note" rows="2" placeholder="أدخل أي ملاحظات..." class="form-input">{{ $EditShiftTime->notes }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                                        <button type="reset" 
                                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i>
                                            <span>إلغاء</span>
                                        </button>

                                        <button type="submit" name="active" value="updateShiftTime"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>تحديث المعلومات</span>
                                        </button>
                                    </div>

                                </div>

                                {!! Form::close() !!}
                            </div>
                               
                                
                            </div>

                        </div>
@endsection
