<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('page-title', isset($seo) && $seo ? ($seo->meta_title ?? $seo->site_name) : config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $canonicalUrl = url()->current();
        $defaultTitle = config('app.name');
        $defaultDescription = 'ConcreteERP نظام لإدارة مصانع الخرسانة الجاهزة — الطلبات، الأسطول، المقاولين، المخزون، الشحنات والتقارير.';
        $defaultKeywords = 'ConcreteERP, نظام ERP, خرسانة جاهزة, إدارة مصانع الخرسانة, طلبات, شحنات, أسطول, مقاولين, مخزون';
    @endphp


    
    @if(isset($seo) && $seo)
        <meta name="description" content="{{ $seo->meta_description }}">
        @if($seo->meta_keywords)<meta name="keywords" content="{{ $seo->meta_keywords }}">@endif
        <meta name="robots" content="{{ $seo->robots ?? 'index, follow' }}">
        <meta name="locale" content="{{ $seo->locale ?? 'ar_IQ' }}">
        @if($seo->canonical_domain)<link rel="canonical" href="{{ rtrim($seo->canonical_domain, '/') }}{{ request()->getRequestUri() == '/' ? '' : request()->getRequestUri() }}">@endif
        <meta property="og:type" content="{{ $seo->og_type ?? 'website' }}">
        <meta property="og:title" content="{{ $seo->og_title ?? $seo->meta_title ?? $seo->site_name }}">
        <meta property="og:description" content="{{ $seo->og_description ?? $seo->meta_description }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ $seo->site_name }}">
        @if($seo->og_image)<meta property="og:image" content="{{ $seo->og_image }}">@endif
        <meta property="og:locale" content="{{ $seo->locale ?? 'ar_IQ' }}">
        <meta name="twitter:card" content="{{ $seo->twitter_card ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo->og_title ?? $seo->meta_title ?? $seo->site_name }}">
        <meta name="twitter:description" content="{{ $seo->og_description ?? $seo->meta_description }}">
        @if($seo->og_image)<meta name="twitter:image" content="{{ $seo->og_image }}">@endif
        @if($seo->twitter_site)<meta name="twitter:site" content="{{ $seo->twitter_site }}">@endif
        @if($seo->extra_meta){!! $seo->extra_meta !!}@endif
        @if($seo->structured_data)<script type="application/ld+json">{!! $seo->structured_data !!}</script>@endif
    @else
        <meta name="description" content="{{ $defaultDescription }}">
        <meta name="keywords" content="{{ $defaultKeywords }}">
        <meta property="og:title" content="{{ $defaultTitle }}">
        <meta property="og:description" content="{{ $defaultDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if(!(isset($seo) && $seo && $seo->canonical_domain))
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if (Auth::user()->account_code == 'cont')
        <link rel="icon" type="image/x-icon"
            href="{{ asset('uploads/contractors_logo/' . Auth::user()->contractor->logo) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset(Auth::user()->CompanyName->logo) }}">
    @endif


    <!-- DNS Prefetch for faster loading -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        $fontFamily = $app_font_family ?? 'Cairo';
        $fontSize = $app_font_size ?? '14';
        $fontParam = str_replace(' ', '+', $fontFamily);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $fontParam }}:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Critical CSS - Load immediately -->
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/perfect-scrollbar.min.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/highlight.min.css') }}">

    <!-- Load critical scripts in head -->
    <script src="{{ asset('assets/js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/tippy-bundle.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>

    <style>
        /* توسيط محتوى الجدول - استبدل الـ CSS القديم بهذا */
        #myTable2 td,
        #myTable2 th {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* توسيط المحتوى داخل عناصر simpleDatatables */
        #myTable2 td>*,
        #myTable2 th>* {
            text-align: center !important;
            display: inline-block;
            width: 100%;
        }

        /* توسيط الروابط والأيقونات */
        #myTable2 td a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* توسيط محتوى الخلايا في simpleDatatables */
        .dataTable-table td,
        .dataTable-table th {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .dataTable-table td>*,
        .dataTable-table th>* {
            text-align: center !important;
        }
        /* خط وحجم الخط من إعدادات النظام */
        body.app-font-custom {
            font-family: '{{ $fontFamily }}', sans-serif !important;
            font-size: {{ $fontSize }}px !important;
        }
    </style>
</head>

<body x-data="main" class="relative overflow-x-hidden font-normal antialiased app-font-custom"
    :class="[$store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ? 'dark' : '',
        $store.app.menu, $store.app.layout, $store.app.rtlClass
    ]">

    <!-- sidebar menu overlay -->
    <div x-cloak class="fixed inset-0 z-50 bg-[black]/60 lg:hidden" :class="{ 'hidden': !$store.app.sidebar }"
        @click="$store.app.toggleSidebar()"></div>

    <!-- screen loader -->
    <div
        class="screen_loader animate__animated fixed inset-0 z-[60] grid place-content-center bg-[#fafafa] dark:bg-[#060818]">
        <svg width="64" height="64" viewBox="0 0 135 135" xmlns="http://www.w3.org/2000/svg" fill="#4361ee">
            <path
                d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s"
                    repeatCount="indefinite"></animateTransform>
            </path>
            <path
                d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s"
                    repeatCount="indefinite"></animateTransform>
            </path>
        </svg>
    </div>

    <!-- scroll to top button -->
    <div class="fixed bottom-6 z-50 ltr:right-6 rtl:left-6" x-data="scrollToTop">
        <template x-if="showTopButton">
            <button type="button"
                class="btn btn-outline-primary animate-pulse rounded-full bg-[#fafafa] p-2 dark:bg-[#060818] dark:hover:bg-primary"
                @click="goToTop">
                <svg width="24" height="24" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                        d="M12 20.75C12.4142 20.75 12.75 20.4142 12.75 20L12.75 10.75L11.25 10.75L11.25 20C11.25 20.4142 11.5858 20.75 12 20.75Z"
                        fill="currentColor"></path>
                    <path
                        d="M6.00002 10.75C5.69667 10.75 5.4232 10.5673 5.30711 10.287C5.19103 10.0068 5.25519 9.68417 5.46969 9.46967L11.4697 3.46967C11.6103 3.32902 11.8011 3.25 12 3.25C12.1989 3.25 12.3897 3.32902 12.5304 3.46967L18.5304 9.46967C18.7449 9.68417 18.809 10.0068 18.6929 10.287C18.5768 10.5673 18.3034 10.75 18 10.75L6.00002 10.75Z"
                        fill="currentColor"></path>
                </svg>
            </button>
        </template>
    </div>

    @include('layouts.setting')

    <div class="main-container min-h-screen text-black dark:text-white-dark" :class="[$store.app.navbar]">
        @include('layouts.sidebar')

        <div class="main-content flex min-h-screen flex-col">
            @include('layouts.header')

            <div class="animate__animated p-6" :class="[$store.app.animation]">
                <div class="pt-5">
                    @include('layouts.flash')
                    @yield('content')
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </div>

    <!-- Load all scripts at the end -->
    <script src="{{ asset('assets/js/highlight.min.js') }}"></script>
    <script src="{{ asset('assets/js/alpine-collaspe.min.js') }}"></script>
    <script src="{{ asset('assets/js/alpine-persist.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-ui.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-focus.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/simple-datatables.js') }}"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // تنسيق النصوص داخل خلايا الجداول
            document.querySelectorAll('table td').forEach(function(cell) {
                let text = cell.innerText.trim();
                let match = text.match(/^\d+(\.\d+)?/);
                if (match) {
                    let num = parseFloat(match[0]);
                    cell.innerText = text.replace(match[0], num.toString());
                }
            });

            // تنسيق قيم الحقول input
            document.querySelectorAll('input[type="text"], input[type="number"]').forEach(function(input) {
                let value = input.value.trim();
                let match = value.match(/^\d+(\.\d+)?/);
                if (match) {
                    let num = parseFloat(match[0]);
                    input.value = value.replace(match[0], num.toString());
                }
            });
        });
    </script>



    <script>
        function formatPrice(input) {
            // إزالة الفواصل القديمة
            let value = input.value.replace(/,/g, '');

            // منع أي أحرف غير رقمية أو فاصلة عشرية
            if (!/^\d*\.?\d*$/.test(value)) {
                input.value = input.value.slice(0, -1);
                return;
            }

            // تقسيم العدد إلى جزء صحيح وعشري
            const parts = value.split('.');
            let integerPart = parts[0];
            const decimalPart = parts[1] ? '.' + parts[1].slice(0, 0) : ''; // رقمين بعد الفاصلة فقط

            // تنسيق الجزء الصحيح بإضافة الفواصل كل 3 أرقام
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            input.value = integerPart + decimalPart;
        }
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            // main section
            Alpine.data('scrollToTop', () => ({
                showTopButton: false,
                init() {
                    window.onscroll = () => {
                        this.scrollFunction();
                    };
                },

                scrollFunction() {
                    if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                        this.showTopButton = true;
                    } else {
                        this.showTopButton = false;
                    }
                },

                goToTop() {
                    document.body.scrollTop = 0;
                    document.documentElement.scrollTop = 0;
                },
            }));

            // theme customization
            Alpine.data('customizer', () => ({
                showCustomizer: false,
            }));

            // sidebar section
            Alpine.data('sidebar', () => ({
                init() {
                    const pathname = window.location.pathname;
                    document.querySelectorAll('.sidebar a[href]').forEach((a) => {
                        try {
                            const u = new URL(a.href);
                            if (u.pathname === pathname) {
                                a.classList.add('active');
                            }
                        } catch (e) { /* ignore */ }
                    });
                },
            }));

            // header section
            Alpine.data('header', () => ({
                init() {
                    const selector = document.querySelector('ul.horizontal-menu a[href="' + window
                        .location.pathname + '"]');
                    if (selector) {
                        selector.classList.add('active');
                        const ul = selector.closest('ul.sub-menu');
                        if (ul) {
                            let ele = ul.closest('li.menu').querySelectorAll('.nav-link');
                            if (ele) {
                                ele = ele[0];
                                setTimeout(() => {
                                    ele.classList.add('active');
                                });
                            }
                        }
                    }
                },

                removeNotification(value) {
                    this.notifications = this.notifications.filter((d) => d.id !== value);
                },

                removeMessage(value) {
                    this.messages = this.messages.filter((d) => d.id !== value);
                },
            }));

            // content section
            Alpine.data('sales', () => ({
                init() {
                    isDark = this.$store.app.theme === 'dark' || this.$store.app.isDarkMode ? true :
                        false;
                    isRtl = this.$store.app.rtlClass === 'rtl' ? true : false;

                    const revenueChart = null;
                    const salesByCategory = null;
                    const dailySales = null;
                    const totalOrders = null;

                    // revenue
                    setTimeout(() => {
                        this.revenueChart = new ApexCharts(this.$refs.revenueChart, this
                            .revenueChartOptions);
                        this.$refs.revenueChart.innerHTML = '';
                        this.revenueChart.render();

                        // sales by category
                        this.salesByCategory = new ApexCharts(this.$refs.salesByCategory, this
                            .salesByCategoryOptions);
                        this.$refs.salesByCategory.innerHTML = '';
                        this.salesByCategory.render();

                        // daily sales
                        this.dailySales = new ApexCharts(this.$refs.dailySales, this
                            .dailySalesOptions);
                        this.$refs.dailySales.innerHTML = '';
                        this.dailySales.render();

                        // total orders
                        this.totalOrders = new ApexCharts(this.$refs.totalOrders, this
                            .totalOrdersOptions);
                        this.$refs.totalOrders.innerHTML = '';
                        this.totalOrders.render();
                    }, 300);

                    this.$watch('$store.app.theme', () => {
                        isDark = this.$store.app.theme === 'dark' || this.$store.app
                            .isDarkMode ? true : false;

                        this.revenueChart.updateOptions(this.revenueChartOptions);
                        this.salesByCategory.updateOptions(this.salesByCategoryOptions);
                        this.dailySales.updateOptions(this.dailySalesOptions);
                        this.totalOrders.updateOptions(this.totalOrdersOptions);
                    });

                    this.$watch('$store.app.rtlClass', () => {
                        isRtl = this.$store.app.rtlClass === 'rtl' ? true : false;
                        this.revenueChart.updateOptions(this.revenueChartOptions);
                    });
                },
            }));
        });
    </script>
    @stack('scripts')
</body>

</html>
