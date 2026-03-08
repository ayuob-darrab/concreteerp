@extends('layouts.app')

@section('page-title', 'تعديل معلومات حساب')

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="grid grid-cols-1 gap-6 pt-5 lg:grid-cols-2" x-data="form">

            @foreach ($accountsType as $item)
                {{-- id', 'city_id', 'company_id', 'breanch_name' --}}
                <div class="panel">
                    <div class="mb-5 flex items-center justify-between">
                        <h5 class="text-lg font-semibold dark:text-white-light">تعديل : {{ $item->typename }}</h5>
                    </div>
                    <div class="mb-5 flex justify-center items-center">
                        <a href="{{ url('accounts/' . $item->code . '&editAccount/edit') }}">
                            <button type="button"
                                class="btn btn-outline-success rounded-full px-8 py-3 text-lg font-semibold shadow-lg">
                                عرض : {{ $item->typename }}
                            </button>
                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

@endsection
