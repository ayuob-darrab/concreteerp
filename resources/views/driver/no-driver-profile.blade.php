@extends('layouts.app')

@section('title', 'لا يوجد ملف سائق')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        <h3 class="mb-3">لا يوجد ملف سائق مرتبط بحسابك</h3>
                        <p class="text-muted mb-4">
                            يبدو أن حسابك غير مرتبط بملف موظف سائق في النظام.
                            <br>
                            يرجى التواصل مع مدير الفرع لربط حسابك بملف السائق.
                        </p>
                        <div class="alert alert-info">
                            <strong>معلومات حسابك:</strong>
                            <br>
                            البريد: {{ auth()->user()->email }}
                            <br>
                            الاسم: {{ auth()->user()->fullname }}
                        </div>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
