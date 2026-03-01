@extends('layouts.app')

@section('page-title', 'تحديث حساب ' . $user->fullname)


@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="mx-auto w-full max-w-[440px]">
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold uppercase !leading-snug text-primary md:text-4xl">تحديث حساب</h1>
                <p class="text-base font-bold leading-normal text-white-dark">تحديث حساب :
                    {{ $user->AccountType->typename . '  -   ' . $user->Usertype->name }}</p>
            </div>

            {!! Form::open([
                'route' => ['accounts.update', $user->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}


            <div>
                <label for="Name">الاسم الثلاثي</label>
                <div class="relative text-white-dark">
                    <input id="Name" type="text" value="{{ $user->fullname }}" required name="fullname"
                        placeholder="الاسم الثلاثي" class="form-input ps-10 placeholder:text-white-dark">
                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                        <svg width="18" height="18" viewbox="0 0 18 18" fill="none">
                            <circle cx="9" cy="4.5" r="3" fill="#888EA8"></circle>
                            <path opacity="0.5"
                                d="M15 13.125C15 14.989 15 16.5 9 16.5C3 16.5 3 14.989 3 13.125C3 11.261 5.68629 9.75 9 9.75C12.3137 9.75 15 11.261 15 13.125Z"
                                fill="#888EA8"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <br>
            <div>
                <label for="Username">اسم المستخدم</label>
                <div class="relative text-white-dark">
                    <input id="Username" type="text" value="{{ $user->username }}" readonly
                        placeholder="اسم المستخدم" name="username"
                        class="form-input ps-10 placeholder:text-white-dark bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                        <svg width="18" height="18" viewbox="0 0 18 18" fill="none">
                            <path opacity="0.5"
                                d="M10.65 2.25H7.35C4.23873 2.25 2.6831 2.25 1.71655 3.23851C0.75 4.22703 0.75 5.81802 0.75 9C0.75 12.182 0.75 13.773 1.71655 14.7615C2.6831 15.75 4.23873 15.75 7.35 15.75H10.65C13.7613 15.75 15.3169 15.75 16.2835 14.7615C17.25 13.773 17.25 12.182 17.25 9C17.25 5.81802 17.25 4.22703 16.2835 3.23851C15.3169 2.25 13.7613 2.25 10.65 2.25Z"
                                fill="currentColor"></path>
                            <path
                                d="M14.3465 6.02574C14.609 5.80698 14.6445 5.41681 14.4257 5.15429C14.207 4.89177 13.8168 4.8563 13.5543 5.07507L11.7732 6.55931C11.0035 7.20072 10.4691 7.6446 10.018 7.93476C9.58125 8.21564 9.28509 8.30993 9.00041 8.30993C8.71572 8.30993 8.41956 8.21564 7.98284 7.93476C7.53168 7.6446 6.9973 7.20072 6.22761 6.55931L4.44652 5.07507C4.184 4.8563 3.79384 4.89177 3.57507 5.15429C3.3563 5.41681 3.39177 5.80698 3.65429 6.02574L5.4664 7.53583C6.19764 8.14522 6.79033 8.63914 7.31343 8.97558C7.85834 9.32604 8.38902 9.54743 9.00041 9.54743C9.6118 9.54743 10.1425 9.32604 10.6874 8.97558C11.2105 8.63914 11.8032 8.14522 12.5344 7.53582L14.3465 6.02574Z"
                                fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fas fa-info-circle"></i> لا يمكن تغيير البريد الإلكتروني بعد إنشاء الحساب
                </p>
            </div>
            <br>
            <br>
            <div>
                <label for="user_type" class="block mb-2 font-semibold">نوع المستخدم</label>
                <div class="relative">
                    <select id="user_type" required name="user_type"
                        class="form-select ps-10 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-800 dark:text-white">
                        <option selected disabled>اختيار نوع المستخدم</option>
                        @foreach ($typeUser as $type)
                            <option value="{{ $type->code }}" {{ $user->usertype_id == $type->code ? 'selected' : '' }}>
                                {{ $type->name }}</option>
                        @endforeach
                    </select>
                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5" />
                            <path d="M6 8L9 11L12 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
            <br>
            {{-- قائمة نوع الموظف --}}
            <div>
                <label for="employee_type" class="block mb-2 font-semibold">نوع الموظف</label>
                <div class="relative">
                    <select id="employee_type" required name="employee_type"
                        class="form-select ps-10 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-800 dark:text-white">
                        <option selected disabled>اختيار نوع الموظف</option>
                        @foreach ($employeeType as $emp)
                            <option value="{{ $emp->id }}" {{ $user->emp_type_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5" />
                            <path d="M6 8L9 11L12 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
            <br>
            <div>
                <label for="is_active" class="block mb-2 font-semibold">حالة الحساب</label>
                <div class="relative">
                    <select id="is_active" required name="is_active"
                        class="form-select ps-10 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-800 dark:text-white">
                        <option selected disabled>اختر الحالة</option>
                        @if (isset($reactivationBlocked) && $reactivationBlocked)
                            <option value="1" disabled>تفعيل (محظور - 48 ساعة)</option>
                        @else
                            <option value="1" {{ $user->is_active ? 'selected' : '' }}>تفعيل</option>
                        @endif
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>تعطيل</option>
                    </select>
                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5" />
                            <path d="M6 8L9 11L12 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                @if (isset($reactivationBlocked) && $reactivationBlocked)
                    <div class="mt-2 p-3 rounded-lg border border-warning/50 bg-warning/10">
                        <p class="text-sm text-warning font-semibold">
                            <i class="fas fa-clock ml-1"></i>
                            لا يمكن إعادة تفعيل هذا الحساب قبل مرور 48 ساعة من التعطيل.
                            المتبقي تقريباً: <strong>{{ $hoursUntilReactivate }}</strong> ساعة.
                        </p>
                    </div>
                @endif
                @if (!$user->is_active && $user->deactivated_by_subscription)
                    <p class="text-xs text-warning mt-1">
                        <i class="fas fa-info-circle"></i> هذا الحساب معطل بسبب الاشتراك.
                    </p>
                @endif
            </div>

            {{-- نهاية قائمة نوع الموظف --}}
            <button type="submit"name="active" value="UpadteUserInformation"
                class="btn btn-gradient !mt-6 w-full border-0 uppercase shadow-[0_10px_20px_-10px_rgba(67,97,238,0.44)]">
                تحديث المعلومات
            </button>
            {!! Form::close() !!}



        </div>

    </div>
@endsection
