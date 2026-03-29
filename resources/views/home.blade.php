@extends('layouts.app')

@section('page-title', 'لوحة التحكم الرئيسية')

@section('content')
    <div class="space-y-6">
        <div class="panel bg-white dark:bg-[#0e1726]">
            <p class="text-black dark:text-white mb-6">
                الوصول السريع: نفس الروابط المتوفرة في القائمة الجانبية، يمكنك فتح أي صفحة من البطاقات أدناه أو من السلايد بار.
            </p>
            @include('layouts.partials.nav-cards')
        </div>
    </div>
@endsection
