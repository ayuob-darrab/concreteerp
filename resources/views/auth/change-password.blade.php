@extends('layouts.app')

@section('page-title', 'تغيير كلمة المرور')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="mx-auto w-full max-w-[440px]">
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold uppercase !leading-snug text-primary md:text-4xl">تغيير كلمة المرور</h1>
                <p class="text-base font-bold leading-normal text-white-dark">{{ Auth::user()->fullname }}</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-success/15 p-4 text-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" autocomplete="off">
                @csrf

                <div class="mb-5">
                    <label for="current_password">كلمة المرور الحالية</label>
                    <div class="relative text-white-dark">
                        <input id="current_password" type="password" name="current_password" required
                            placeholder="أدخل كلمة المرور الحالية" autocomplete="current-password"
                            class="form-input ps-10 @error('current_password') border-danger @enderror">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="password">كلمة المرور الجديدة</label>
                    <div class="relative text-white-dark">
                        <input id="password" type="password" name="password" required
                            placeholder="أدخل كلمة المرور الجديدة" autocomplete="new-password"
                            class="form-input ps-10 @error('password') border-danger @enderror">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                    <div class="relative text-white-dark">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="أعد إدخال كلمة المرور الجديدة" autocomplete="new-password"
                            class="form-input ps-10">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full">تغيير كلمة المرور</button>
            </form>
        </div>
    </div>
@endsection
