        @php
            $reqUri = request()->getRequestUri();
            $reqPath = request()->path();
            $basePath = $reqPath ? rtrim(preg_replace('#/'.preg_quote($reqPath, '#').'$#', '', $reqUri), '/') : rtrim($reqUri, '/');
            if ($basePath === '') { $basePath = rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/'); }
            $u = function($path) use ($basePath) { return ($basePath ? $basePath.'/' : '').ltrim($path ?? '', '/'); };
            $r = function($name, $params = []) use ($basePath) { return $basePath . parse_url(route($name, $params), PHP_URL_PATH); };
        @endphp
        <div :class="{ 'dark text-white-dark': $store.app.semidark }">
            <nav x-data="sidebar"
                class="sidebar fixed bottom-0 top-0 z-50 h-full min-h-screen w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300">
                <div class="h-full bg-white dark:bg-[#0e1726]">
                    <div class="flex items-center justify-between px-4 py-3">

                        @if (Auth::user()->account_code == 'cont')
                            <a href="{{ $basePath ?: '/' }}" class="main-logo flex shrink-0 items-center">
                                <img class="inline w-8 ltr:-ml-1 rtl:-mr-1"
                                    src="{{ asset('uploads/contractors_logo/' . Auth::user()->contractor->logo) }}"alt="image">
                                <span
                                    class="align-middle text-2xl font-semibold ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light lg:inline">{{ Auth::user()->contractor->contract_name }}</span>
                            </a>
                        @else
                            <a href="{{ $basePath ?: '/' }}" class="main-logo flex shrink-0 items-center">
                                <img class="inline w-8 ltr:-ml-1 rtl:-mr-1"
                                    src="{{ asset(Auth::user()->CompanyName->logo) }}"alt="image">
                                <span
                                    class="align-middle text-2xl font-semibold ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light lg:inline">{{ Auth::user()->CompanyName->name }}</span>
                            </a>
                        @endif



                        <a href="javascript:;"
                            class="collapse-icon flex h-8 w-8 items-center rounded-full transition duration-300 hover:bg-gray-500/10 rtl:rotate-180 dark:text-white-light dark:hover:bg-dark-light/10"
                            @click="$store.app.toggleSidebar()">
                            <svg class="m-auto h-5 w-5" width="20" height="20" viewbox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                    <ul class="perfect-scrollbar relative h-[calc(100vh-80px)] space-y-0.5 overflow-y-auto overflow-x-hidden p-4 py-0 font-semibold"
                        x-data="{ activeDropdown: 'dashboard' }">
                    

                        @if (Auth::user()->account_code != 'cont')
                        <h2
                            class="-mx-4 mb-1 flex items-center bg-white-light/30 px-7 py-3 font-extrabold uppercase dark:bg-dark dark:bg-opacity-[0.08]">
                            <svg class="hidden h-5 w-4 flex-none" viewbox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>الإدارة العليا</span>
                        </h2>
                        @endif

                        @if (Auth::user()->usertype_id == 'SA' && Auth::user()->company_code == 'SA' && Auth::user()->account_code == 'SA')
                            <li class="nav-item">
                                <ul>
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group"
                                            :class="{ 'active': activeDropdown === 'SA-Companies' }"
                                            @click="activeDropdown === 'SA-Companies' ? activeDropdown = null : activeDropdown = 'SA-Companies'">
                                            <div class="flex items-center">
                                                <!-- أيقونة معلومات -->
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark"
                                                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                        fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12 8C12.5523 8 13 7.55228 13 7C13 6.44772 12.5523 6 12 6C11.4477 6 11 6.44772 11 7C11 7.55228 11.4477 8 12 8ZM11.25 10C11.25 9.58579 11.5858 9.25 12 9.25C12.4142 9.25 12.75 9.58579 12.75 10V17C12.75 17.4142 12.4142 17.75 12 17.75C11.5858 17.75 11.25 17.4142 11.25 17V10Z"
                                                        fill="currentColor" />
                                                </svg>

                                                <span
                                                    class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">
                                                    الشركات
                                                </span>
                                            </div>

                                            <div class="rtl:rotate-180"
                                                :class="{ '!rotate-90': activeDropdown === 'SA-Companies' }">
                                                <svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                            </div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Companies'" x-collapse class="sub-menu text-gray-500">
                                    


                                            <li><a href="{{ $u('companies/ListCompanies') }}">إضافة شركة</a></li>
                                            <li><a href="{{ $u('companies/listAccountsCompanies') }}">حسابات الشركات</a></li>
                                        </ul>
                                    </li>

                                    {{-- الاشتراكات --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Subscriptions' }" @click="activeDropdown === 'SA-Subscriptions' ? activeDropdown = null : activeDropdown = 'SA-Subscriptions'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke="currentColor" stroke-width="1.5"/><path opacity="0.5" d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">الاشتراكات</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Subscriptions' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Subscriptions'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('subscriptions/plans') }}">خطط الاشتراك</a></li>
                                            <li><a href="{{ $u('subscriptions/companies') }}">إدارة اشتراكات الشركات</a></li>
                                            <li><a href="{{ $u('subscriptions/settings') }}">إعدادات الأسعار</a></li>
                                            <li><a href="{{ $u('subscriptions/financial-reports') }}">التقارير المالية</a></li>
                                            <li><a href="{{ $u('subscriptions/monitor') }}">مراقبة الاشتراكات</a></li>
                                        </ul>
                                    </li>

                                    {{-- الدفع الإلكتروني --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Payment' }" @click="activeDropdown === 'SA-Payment' ? activeDropdown = null : activeDropdown = 'SA-Payment'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke="currentColor" stroke-width="1.5"/><path d="M7 12H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">الدفع الإلكتروني</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Payment' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Payment'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('payment-cards') }}">حسابات الدفع الإلكتروني</a></li>
                                            <li><a href="{{ $u('payment-cards-report/transactions') }}">تقرير المعاملات</a></li>
                                        </ul>
                                    </li>

                                    {{-- إدارة المستخدمين --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Users' }" @click="activeDropdown === 'SA-Users' ? activeDropdown = null : activeDropdown = 'SA-Users'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="6" r="4" stroke="currentColor" stroke-width="1.5"/><path opacity="0.5" d="M15 9.5C16.3807 9.5 17.5 8.38071 17.5 7C17.5 5.61929 16.3807 4.5 15 4.5" stroke="currentColor" stroke-width="1.5"/><path d="M2 22C2 17.5228 5.52285 14 10 14C14.4772 14 18 17.5228 18 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path opacity="0.5" d="M15 14C16.0182 14 16.985 14.1497 17.8835 14.4236" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">إدارة المستخدمين</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Users' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Users'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('admin/users') }}">جميع المستخدمين</a></li>
                                            <li><a href="{{ $u('admin/roles') }}">الأدوار والصلاحيات</a></li>
                                            <li><a href="{{ $u('admin/activity-logs') }}">سجلات النشاط</a></li>
                                        </ul>
                                    </li>

                                    {{-- التقارير والإحصائيات --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Reports' }" @click="activeDropdown === 'SA-Reports' ? activeDropdown = null : activeDropdown = 'SA-Reports'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 10V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 13V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 7V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">التقارير والإحصائيات</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Reports' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Reports'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('admin/statistics') }}">إحصائيات النظام</a></li>
                                            <li><a href="{{ $u('admin/performance') }}">تقارير الأداء</a></li>
                                        </ul>
                                    </li>

                                    {{-- إعدادات النظام --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Settings' }" @click="activeDropdown === 'SA-Settings' ? activeDropdown = null : activeDropdown = 'SA-Settings'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5"/><path d="M19.622 10.3954L18.5247 7.7448C17.7902 5.82119 16.0612 4.67814 14.0052 4.67814H9.9948C7.93882 4.67814 6.20974 5.82119 5.47528 7.7448L4.37798 10.3954C3.19638 11.2523 2.64355 12.6419 2.93489 14.0001L3.55007 16.5712C3.87319 18.083 5.29676 19.1781 6.84432 19.1781H17.1557C18.7032 19.1781 20.1268 18.083 20.4499 16.5712L21.0651 14.0001C21.3564 12.6419 20.8036 11.2523 19.622 10.3954Z" stroke="currentColor" stroke-width="1.5"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">إعدادات النظام</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Settings' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Settings'" x-collapse class="sub-menu text-gray-500">
                                        <li><a href="{{ $u('admin/super-admin-users') }}">إدارة حسابات السوبر أدمن</a></li>
                                            <li><a href="{{ $u('admin/settings') }}">الإعدادات العامة</a></li>
                                            <li><a href="{{ $u('admin/seo') }}">إدارة SEO (محركات البحث)</a></li>
                                            <li><a href="{{ $u('admin/backups') }}">النسخ الاحتياطي</a></li>
                                            <li><a href="{{ $u('admin/notifications/list') }}">إدارة الإشعارات</a></li>
                                        </ul>
                                    </li>

                                    {{-- البيانات الأساسية --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-MasterData' }" @click="activeDropdown === 'SA-MasterData' ? activeDropdown = null : activeDropdown = 'SA-MasterData'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 12H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 18H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">البيانات الأساسية</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-MasterData' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-MasterData'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('admin/cities') }}">المحافظات</a></li>
                                            <li><a href="{{ $u('admin/employee-types') }}">أنواع الموظفين</a></li>
                                            <li><a href="{{ $u('materials/listmeasurement_units') }}">وحدات القياس</a></li>
                                            <li><a href="{{ $u('materials/ConcreteMix') }}">أنواع الخرسانة</a></li>
                                            <li><a href="{{ $u('pricing-categories') }}">الفئات السعرية</a></li>
                                        </ul>
                                    </li>

                                    {{-- الدعم والصيانة --}}
                                    <li class="menu nav-item">
                                        <button type="button" class="nav-link group" :class="{ 'active': activeDropdown === 'SA-Support' }" @click="activeDropdown === 'SA-Support' ? activeDropdown = null : activeDropdown = 'SA-Support'">
                                            <div class="flex items-center">
                                                <svg class="shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690] dark:group-hover:text-white-dark" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5"/><path d="M10.125 10.125C10.125 8.84732 11.2223 7.75 12.5 7.75C13.7777 7.75 14.875 8.84732 14.875 10.125C14.875 11.4027 13.7777 12.5 12.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12.5 14.875V15.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">الدعم والصيانة</span>
                                            </div>
                                            <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'SA-Support' }"><svg width="16" height="16" viewbox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                        </button>
                                        <ul x-cloak x-show="activeDropdown === 'SA-Support'" x-collapse class="sub-menu text-gray-500">
                                            <li><a href="{{ $u('admin/tickets') }}">تذاكر الدعم</a></li>
                                            <li><a href="{{ $u('admin/error-logs') }}">سجل الأخطاء</a></li>
                                            <li><a href="{{ $u('admin/system-health') }}">صحة النظام</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif


                        {{-- مسولية مدير الشركه --}}

                        {{-- @if (Auth::user()->usertype_id != 'SA' && Auth::user()->company_code != 'SA' && Auth::user()->account_code == 'SA') --}}

                        @if (Auth::user()->account_code != 'cont')
                            <li class="menu nav-item">
                                <ul>

                                    @if (Auth::user()->usertype_id == 'CM')
                                        {{-- مدير الشركة --}}
                                        <li class="menu nav-item">
                                            <a href="{{ $u('home') }}" class="nav-link group">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.5" d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z" fill="currentColor" />
                                                        <path d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z" fill="currentColor" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">لوحة التحكم</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="menu nav-item">
                                            <a href="{{ $r('companyBranch.company.orders.dashboard') }}" class="nav-link group">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5C15 6.10457 14.1046 7 13 7H11C9.89543 7 9 6.10457 9 5Z" stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">الطلبات لكل الأفرع</span>
                                                </div>
                                            </a>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'companyManage' }"
                                                @click="activeDropdown === 'companyManage' ? activeDropdown = null : activeDropdown = 'companyManage'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 22H22" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M17 22V6C17 4.11438 17 3.17157 16.4142 2.58579C15.8284 2 14.8856 2 13 2H11C9.11438 2 8.17157 2 7.58579 2.58579C7 3.17157 7 4.11438 7 6V22"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path opacity="0.5"
                                                            d="M22 22V11C22 9.11438 22 8.17157 21.4142 7.58579C20.8284 7 19.8856 7 18 7H17V22"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path opacity="0.5"
                                                            d="M7 22V11C7 9.11438 7 8.17157 6.41421 7.58579C5.82843 7 4.88562 7 3 7C2.44772 7 2 7.44772 2 8V22"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M10 6H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M10 10H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M10 14H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M10 18H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">إدارة الشركة</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'companyManage' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'companyManage'" x-collapse class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('companyBranch/Allbranch') }}">الأفرع</a></li>
                                                <li><a href="{{ $u('Employees/ListEmployees') }}">الموظفين</a></li>
                                                <li><a href="{{ $u('accounts/listaccount') }}">حسابات المستخدمين</a></li>
                                                <li><a href="{{ $u('companies/ShiftTimes') }}">شفتات العمل</a></li>
                                                <li class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-1">الحضور والانصراف</span>
                                                    <a href="{{ $r('attendance.admin.report') }}" class="block py-1.5 px-3 rounded hover:bg-gray-100 dark:hover:bg-gray-700">📋 عرض الحضور لكل الفروع</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'contractors' }"
                                                @click="activeDropdown === 'contractors' ? activeDropdown = null : activeDropdown = 'contractors'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="9" cy="6" r="4"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path opacity="0.5"
                                                            d="M15 9C16.6569 9 18 7.65685 18 6C18 4.34315 16.6569 3 15 3"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <ellipse cx="9" cy="17" rx="7"
                                                            ry="4" stroke="currentColor"
                                                            stroke-width="1.5" />
                                                        <path opacity="0.5"
                                                            d="M18 14C19.7542 14.3847 21 15.3589 21 16.5C21 17.5293 19.9863 18.4229 18.5 18.8704"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">العملاء والموردين</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'contractors' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'contractors'" x-collapse class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('contractors/List') }}">المقاولين</a></li>
                                                <li><a href="{{ $u('warehouse/addSupplier') }}">موردي المواد</a></li>
                                            </ul>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'resources' }"
                                                @click="activeDropdown === 'resources' ? activeDropdown = null : activeDropdown = 'resources'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 8L12 3L21 8V16L12 21L3 16V8Z" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M12 12L21 7" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 12V21" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 12L3 7" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">المخزن والمنتجات</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'resources' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'resources'" x-collapse class="sub-menu text-gray-500">
                                                <li class="text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-1">المنتجات</li>
                                                <li><a href="{{ $u('warehouse/CompanyListConcreteMix') }}">الخرسانة</a></li>
                                                <li><a href="{{ $u('company-prices') }}">أسعار الفئات</a></li>
                                                <li class="text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-1 mt-2 border-t border-gray-200 dark:border-gray-600 pt-2">المواد الأولية</li>
                                                <li><a href="{{ $u('warehouse/addMainMaterials') }}">المواد الأساسية</a></li>
                                                <li><a href="{{ $u('warehouse/listchemicals') }}">المواد الكيميائية</a></li>
                                                <li><a href="{{ $u('materials/listMaterialEquipment') }}">سعات المواد</a></li>
                                                <li class="text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-1 mt-2 border-t border-gray-200 dark:border-gray-600 pt-2">الأسطول</li>
                                                <li><a href="{{ $u('car-types') }}">أنواع السيارات</a></li>
                                                <li><a href="{{ $u('cars/ListCar') }}">السيارات</a></li>
                                            </ul>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'supportNotifications' }"
                                                @click="activeDropdown === 'supportNotifications' ? activeDropdown = null : activeDropdown = 'supportNotifications'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M18.75 9V9.704C18.75 10.401 19.023 11.07 19.508 11.573L20.696 12.802C21.652 13.791 21.652 15.378 20.696 16.367C19.075 18.044 16.761 19 14.357 19H9.643C7.239 19 4.925 18.044 3.304 16.367C2.348 15.378 2.348 13.791 3.304 12.802L4.492 11.573C4.977 11.07 5.25 10.401 5.25 9.704V9C5.25 5.272 8.273 2.25 12 2.25C15.727 2.25 18.75 5.272 18.75 9Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path opacity="0.5"
                                                            d="M10.5 2.5V3C10.5 3.828 11.172 4.5 12 4.5C12.828 4.5 13.5 3.828 13.5 3V2.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M7.5 19C7.5 21.5 9.5 22 12 22C14.5 22 16.5 21.5 16.5 19"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">الإشعارات والدعم</span>
                                                    @php
                                                        $newNotificationsCount = \App\Models\Notification::where(
                                                            function ($q) {
                                                                $q->where(
                                                                    'company_code',
                                                                    Auth::user()->company_code,
                                                                )->orWhere('company_code', 'ALL');
                                                            },
                                                        )
                                                            ->where('is_read', false)
                                                            ->count();
                                                        $openTicketsCount = \App\Models\SupportTicket::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->whereIn('status', [
                                                                'open',
                                                                'in_progress',
                                                                'pending_response',
                                                            ])
                                                            ->count();
                                                    @endphp
                                                    @if ($newNotificationsCount > 0)
                                                        <span
                                                            class="badge bg-danger rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">{{ $newNotificationsCount > 99 ? '99+' : $newNotificationsCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'supportNotifications' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'supportNotifications'" x-collapse class="sub-menu text-gray-500">
                                                <li>
                                                    <a href="{{ $u('company/notifications') }}" class="flex items-center justify-between">
                                                        <span>إشعارات النظام</span>
                                                        @if ($newNotificationsCount > 0)
                                                            <span class="badge bg-primary text-white rounded-full px-2 text-xs">{{ $newNotificationsCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('support') }}" class="flex items-center justify-between">
                                                        <span>تذاكر الدعم</span>
                                                        @if ($openTicketsCount > 0)
                                                            <span class="badge bg-warning text-white rounded-full px-2 text-xs">{{ $openTicketsCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <!-- <li><a href="{{ $u('support/create') }}">تذكرة جديدة</a></li> -->
                                            </ul>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'companyPayments' }"
                                                @click="activeDropdown === 'companyPayments' ? activeDropdown = null : activeDropdown = 'companyPayments'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">المدفوعات والبطاقات</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'companyPayments' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'companyPayments'" x-collapse class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('company-payment-cards') }}">بطاقات الدفع</a></li>
                                                <!-- <li><a href="{{ $u('company-payment-cards/create') }}">إضافة بطاقة</a></li> -->
                                                <li><a href="{{ $u('company-payment-cards-report/transactions') }}">تقرير المعاملات</a></li>
                                                <li class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                                                    <a href="{{ $u('branch/payments/report') }}">تقرير المقبوضات</a>
                                                </li>
                                                <li><a href="{{ $u('branch/payments/branches-report') }}">تقرير الفروع</a></li>
                                            </ul>
                                        </li>

                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'companyReports' }"
                                                @click="activeDropdown === 'companyReports' ? activeDropdown = null : activeDropdown = 'companyReports'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2H6C4.89543 2 4 2.89543 4 4Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M14 2V8H20" stroke="currentColor"
                                                            stroke-width="1.5" />
                                                        <path d="M8 13H16" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M8 17H12" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">التقارير المالية</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'companyReports' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'companyReports'" x-collapse class="sub-menu text-gray-500">
                                                <li><a href="{{ $r('financial-report.index') }}">تقرير الطلبات</a></li>
                                                <li><a href="{{ $u('financial/reports/daily') }}">التقرير اليومي</a></li>
                                            </ul>
                                        </li>
                                    @endif

                                    @if (Auth::user()->usertype_id == 'BM')
                                        {{-- 1. لوحة التحكم لمدير الفرع --}}
                                        <li class="menu nav-item">
                                            <a href="{{ $u('home') }}" class="nav-link group">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.5"
                                                            d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">🏠
                                                        لوحة التحكم</span>
                                                </div>
                                            </a>
                                        </li>

                                        {{-- 2. إدارة الطلبات --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchOrders' }"
                                                @click="activeDropdown === 'branchOrders' ? activeDropdown = null : activeDropdown = 'branchOrders'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path
                                                            d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5C15 6.10457 14.1046 7 13 7H11C9.89543 7 9 6.10457 9 5Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M9 12L11 14L15 10" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">📋
                                                        إدارة الطلبات</span>
                                                    @php
                                                        $newRequestOrdersCount = \App\Models\WorkOrder::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->where('status_code', 'new')
                                                            ->whereNull('branch_approval_status')
                                                            ->count();

                                                        $approvedByContractorCount = \App\Models\WorkOrder::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->where('branch_approval_status', 'approved')
                                                            ->where('requester_approval_status', 'approved')
                                                            ->where('status_code', 'new')
                                                            ->count();

                                                        $totalBranchOrdersCount =
                                                            $newRequestOrdersCount + $approvedByContractorCount;
                                                    @endphp
                                                    @if ($totalBranchOrdersCount > 0)
                                                        <span
                                                            class="badge bg-danger rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">{{ $totalBranchOrdersCount > 99 ? '99+' : $totalBranchOrdersCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchOrders' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchOrders'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li>
                                                    <a href="{{ $u('companyBranch/directRequest') }}"
                                                        class="flex items-center justify-between">
                                                        <span>⚡ طلب مباشر</span>
                                                    </a>
                                                </li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <li>
                                                    <a href="{{ $u('companyBranch/listNewRequestOrders') }}"
                                                        class="flex items-center justify-between">
                                                        <span>🆕 الطلبات الجديدة</span>
                                                        @if ($newRequestOrdersCount > 0)
                                                            <span
                                                                class="badge bg-danger text-white rounded-full px-2 text-xs">{{ $newRequestOrdersCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('companyBranch/listApprovedByContractor') }}"
                                                        class="flex items-center justify-between">
                                                        <span>✅ بانتظار الموافقة النهائية</span>
                                                        @if ($approvedByContractorCount > 0)
                                                            <span
                                                                class="badge bg-success text-white rounded-full px-2 text-xs">{{ $approvedByContractorCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                @php
                                                    $inProgressOrdersCount = \App\Models\WorkOrder::where(
                                                        'company_code',
                                                        Auth::user()->company_code,
                                                    )
                                                        ->where('branch_id', Auth::user()->branch_id)
                                                        ->where('status_code', 'in_progress')
                                                        ->count();
                                                @endphp
                                                <li>
                                                    <a href="{{ $u('companyBranch/ordersInProgress') }}"
                                                        class="flex items-center justify-between">
                                                        <span>🚧 قيد العمل</span>
                                                        @if ($inProgressOrdersCount > 0)
                                                            <span
                                                                class="badge bg-warning text-dark rounded-full px-2 text-xs">{{ $inProgressOrdersCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('companyBranch/ordersCompleted') }}">
                                                        <span>📦 المكتملة</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        {{-- 2.5 المدفوعات --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchPayments' }"
                                                @click="activeDropdown === 'branchPayments' ? activeDropdown = null : activeDropdown = 'branchPayments'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 6V18" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M15 9.5C15 8.11929 13.6569 7 12 7C10.3431 7 9 8.11929 9 9.5C9 10.8807 10.3431 12 12 12C13.6569 12 15 13.1193 15 14.5C15 15.8807 13.6569 17 12 17C10.3431 17 9 15.8807 9 14.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">💰
                                                        المدفوعات</span>
                                                    @php
                                                        $unpaidCustomersCount = \App\Models\WorkOrder::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->whereIn('status_code', ['in_progress', 'completed'])
                                                            ->where(function ($q) {
                                                                $q->where('payment_status', '!=', 'paid')
                                                                    ->orWhereNull('payment_status');
                                                            })
                                                            ->distinct('customer_phone')
                                                            ->count('customer_phone');
                                                    @endphp
                                                    @if ($unpaidCustomersCount > 0)
                                                        <span
                                                            class="badge bg-danger rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">{{ $unpaidCustomersCount > 99 ? '99+' : $unpaidCustomersCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchPayments' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchPayments'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li>
                                                    <a href="{{ $u('branch/payments') }}"
                                                        class="flex items-center justify-between">
                                                        <span>💳 دفعات الزبائن</span>
                                                        @if ($unpaidCustomersCount > 0)
                                                            <span
                                                                class="badge bg-danger text-white rounded-full px-2 text-xs">{{ $unpaidCustomersCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('branch/payments/report') }}">
                                                        <span>📊 تقرير المقبوضات</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        {{-- 3. أوامر العمل والتنفيذ --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'workExecution' }"
                                                @click="activeDropdown === 'workExecution' ? activeDropdown = null : activeDropdown = 'workExecution'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 6V18" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M15 9.5C15 8.11929 13.6569 7 12 7C10.3431 7 9 8.11929 9 9.5C9 10.8807 10.3431 12 12 12C13.6569 12 15 13.1193 15 14.5C15 15.8807 13.6569 17 12 17C10.3431 17 9 15.8807 9 14.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">⚙️
                                                        أوامر العمل والتنفيذ</span>
                                                    @php
                                                        $pendingJobsCount = \App\Models\WorkJob::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->whereIn('status', ['pending', 'materials_reserved'])
                                                            ->count();

                                                        $activeJobsCount = \App\Models\WorkJob::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->where('status', 'in_progress')
                                                            ->count();

                                                        $todayJobsCount = \App\Models\WorkJob::where(
                                                            'company_code',
                                                            Auth::user()->company_code,
                                                        )
                                                            ->where('branch_id', Auth::user()->branch_id)
                                                            ->whereDate('scheduled_date', today())
                                                            ->whereIn('status', [
                                                                'pending',
                                                                'materials_reserved',
                                                                'in_progress',
                                                            ])
                                                            ->count();
                                                    @endphp
                                                    @if ($pendingJobsCount + $activeJobsCount > 0)
                                                        <span
                                                            class="badge bg-info rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">{{ $pendingJobsCount + $activeJobsCount }}</span>
                                                    @endif
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'workExecution' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'workExecution'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li>
                                                    <a href="{{ $u('companyBranch/execution/dashboard') }}"
                                                        class="flex items-center justify-between">
                                                        <span>📊 لوحة التحكم</span>
                                                    </a>
                                                </li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <li>
                                                    <a href="{{ $u('companyBranch/workJobs/today') }}"
                                                        class="flex items-center justify-between">
                                                        <span>📅 أعمال اليوم</span>
                                                        @if ($todayJobsCount > 0)
                                                            <span
                                                                class="badge bg-primary text-white rounded-full px-2 text-xs">{{ $todayJobsCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('companyBranch/workJobs/pending') }}"
                                                        class="flex items-center justify-between">
                                                        <span>⏳ بانتظار التنفيذ</span>
                                                        @if ($pendingJobsCount > 0)
                                                            <span
                                                                class="badge bg-warning text-dark rounded-full px-2 text-xs">{{ $pendingJobsCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('companyBranch/workJobs/active') }}"
                                                        class="flex items-center justify-between">
                                                        <span>🚧 قيد التنفيذ</span>
                                                        @if ($activeJobsCount > 0)
                                                            <span
                                                                class="badge bg-info text-white rounded-full px-2 text-xs">{{ $activeJobsCount }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <li>
                                                    <a href="{{ $u('companyBranch/workJobs/completed') }}">
                                                        <span>✅ المكتملة</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $u('companyBranch/workShipments') }}">
                                                        <span>🚛 الشحنات</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        {{-- 4. إدارة الفرع --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchManagement' }"
                                                @click="activeDropdown === 'branchManagement' ? activeDropdown = null : activeDropdown = 'branchManagement'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 22H22" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M17 22V6C17 4.11438 17 3.17157 16.4142 2.58579C15.8284 2 14.8856 2 13 2H11C9.11438 2 8.17157 2 7.58579 2.58579C7 3.17157 7 4.11438 7 6V22"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path d="M10 6H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M10 10H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M10 14H14" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">🏢
                                                        إدارة الفرع</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchManagement' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchManagement'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('companyBranch/BranchManage') }}">⚙️ إعدادات
                                                        الفرع</a></li>
                                                <li><a href="{{ $u('accounts/listBranchaccounts') }}">👥
                                                        المستخدمين</a></li>
                                                <li><a href="{{ $u('Employees/listBranchemployees') }}">👷 موظفين
                                                        الفرع</a></li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <li class="font-semibold text-gray-700 dark:text-gray-300 px-3 mt-2">
                                                    الحضور والانصراف</li>
                                                <!-- <li><a href="{{ $u('attendance/admin/dashboard') }}">📊 لوحة
                                                        الحضور</a></li> -->
                                                <li><a href="{{ $u('attendance/admin/report') }}">📋 تقرير الحضور</a>
                                                </li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <!-- <li>
                                                    <a href="{{ $u('advances/pending') }}"
                                                        class="flex items-center justify-between">
                                                        <span>⏳ الموافقة على السلف</span>
                                                        @if (isset($pendingAdvancesCount) && $pendingAdvancesCount > 0)
                                                            <span
                                                                class="badge bg-danger rounded-full px-2 py-0.5 text-xs text-white">{{ $pendingAdvancesCount }}</span>
                                                        @endif
                                                    </a>
                                                </li> -->
                                            </ul>
                                        </li>

                                     

                                        {{-- 5. المخزون والمواد --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchInventory' }"
                                                @click="activeDropdown === 'branchInventory' ? activeDropdown = null : activeDropdown = 'branchInventory'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 8L12 3L21 8V16L12 21L3 16V8Z" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M12 12L21 7" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 12V21" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 12L3 7" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">📦
                                                        المخزون والمواد</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchInventory' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchInventory'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('warehouse/BranchConcreteMix') }}">🧱 أنواع
                                                        الخرسانة</a></li>
                                                <li><a href="{{ $u('warehouse/addMainMaterialsBranch') }}">📦 المواد
                                                        الأساسية</a></li>
                                                <li><a href="{{ $u('warehouse/Branchlistchemicals') }}">🧪 المواد
                                                        الكيميائية</a></li>
                                                <li><a href="{{ $u('warehouse/addSupplier') }}">موردي المواد</a></li>
                                                <li><a href="{{ $u('car-maintenance') }}">🔧 صيانة السيارات</a></li>
                                            </ul>
                                        </li>

                                        {{-- 6. العملاء والمقاولين --}}
                                        <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchCustomers' }"
                                                @click="activeDropdown === 'branchCustomers' ? activeDropdown = null : activeDropdown = 'branchCustomers'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="9" cy="6" r="4"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path
                                                            d="M15 9C16.6569 9 18 7.65685 18 6C18 4.34315 16.6569 3 15 3"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <ellipse cx="9" cy="17" rx="7"
                                                            ry="4" stroke="currentColor"
                                                            stroke-width="1.5" />
                                                        <path
                                                            d="M18 14C19.7542 14.3847 21 15.3589 21 16.5C21 17.5293 19.9863 18.4229 18.5 18.8704"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">👥
                                                        العملاء والمقاولين</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchCustomers' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchCustomers'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('contractors/List') }}">👷 المقاولين</a></li>
                                            </ul>
                                        </li>

                                        {{-- 7. السلف والقروض (مخفية) --}}
                                        {{-- <li class="menu nav-item">
                                            <button type="button" class="nav-link group"
                                                :class="{ 'active': activeDropdown === 'branchAdvances' }"
                                                @click="activeDropdown === 'branchAdvances' ? activeDropdown = null : activeDropdown = 'branchAdvances'">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 6V18" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M15 9.5C15 8.11929 13.6569 7 12 7C10.3431 7 9 8.11929 9 9.5C9 10.8807 10.3431 12 12 12C13.6569 12 15 13.1193 15 14.5C15 15.8807 13.6569 17 12 17C10.3431 17 9 15.8807 9 14.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <circle cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            opacity="0.5" />
                                                    </svg>
                                                    <span
                                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">💰
                                                        السلف والقروض</span>
                                                </div>
                                                <div class="rtl:rotate-180"
                                                    :class="{ '!rotate-90': activeDropdown === 'branchAdvances' }">
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <path d="M9 5L15 12L9 19" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </button>
                                            <ul x-cloak x-show="activeDropdown === 'branchAdvances'" x-collapse
                                                class="sub-menu text-gray-500">
                                                <li><a href="{{ $u('advances/approved') }}">💵 السلف (للدفع)</a></li>
                                                <li><a href="{{ $u('advances/create') }}">➕ طلب سلفة جديدة</a></li>
                                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                                <li><a href="{{ $u('advances') }}">📋 جميع السلف</a></li>
                                                <li><a href="{{ $u('advances?status=completed') }}">✅ السلف
                                                        المكتملة</a></li>
                                                <li><a href="{{ $u('advances/settings/manage') }}">⚙️ الإعدادات</a>
                                                </li>
                                            </ul>
                                        </li> --}}

                                        {{-- 8. التقارير — مخفية عن مدير الفرع BM حسب الصلاحيات --}}
                                    @endif

                                </ul>
                            </li>
                        @endif
                        {{-- نهاية قوائم مدير الشركة ومدير الفرع --}}

                        @if (Auth::user()->account_code == 'cont')
                            {{-- 1. لوحة التحكم للمقاول --}}
                            <li class="menu nav-item">
                                <a href="{{ $u('/home') }}') }}" class="nav-link group">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.5"
                                                d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                                fill="currentColor" />
                                            <path
                                                d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                                fill="currentColor" />
                                        </svg>
                                        <span
                                            class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">🏠
                                            لوحة التحكم</span>
                                    </div>
                                </a>
                            </li>

                            {{-- 2. الطلبات (كاملة - لا تعديل) --}}
                            <li class="menu nav-item">
                                <button type="button" class="nav-link group"
                                    :class="{ 'active': activeDropdown === 'contractorOrders' }"
                                    @click="activeDropdown === 'contractorOrders' ? activeDropdown = null : activeDropdown = 'contractorOrders'">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 6H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round"></path>
                                            <path d="M3 12H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round"></path>
                                            <path d="M3 18H21" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round"></path>
                                        </svg>
                                        <span
                                            class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">📋
                                            طلباتي</span>
                                        @php
                                            $pendingApprovalCount = \App\Models\WorkOrder::where('sender_type', 'cont')
                                                ->where('sender_id', Auth::user()->id)
                                                ->where('branch_approval_status', 'approved')
                                                ->whereNull('requester_approval_status')
                                                ->count();
                                        @endphp
                                        @if ($pendingApprovalCount > 0)
                                            <span
                                                class="badge bg-danger rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">{{ $pendingApprovalCount }}</span>
                                        @endif
                                    </div>
                                    <div class="rtl:rotate-180"
                                        :class="{ '!rotate-90': activeDropdown === 'contractorOrders' }">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </button>
                                <ul x-cloak x-show="activeDropdown === 'contractorOrders'" x-collapse
                                    class="sub-menu text-gray-500">
                                    <li><a href="{{ $u('contractors/SendRequestsContractor') }}">➕ تقديم طلب جديد</a>
                                    </li>
                                    <li><a href="{{ $u('contractors/MyPendingOrders') }}">📋 طلباتي الجديدة</a>
                                    </li>
                                    <li>
                                        <a href="{{ $u('contractors/CheckRequestsContractor') }}"
                                            class="flex items-center justify-between">
                                            <span>⏳ بانتظار موافقتي</span>
                                            @if ($pendingApprovalCount > 0)
                                                <span
                                                    class="badge bg-danger text-white rounded-full px-2 text-xs">{{ $pendingApprovalCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><a href="{{ $u('contractors/ApprovedOrders') }}">🚧 قيد العمل</a>
                                    </li>
                                </ul>
                            </li>

                            {{-- 3. فواتير الطلبات --}}
                            <li class="menu nav-item">
                                <a href="{{ $r('contractor-invoices.index') }}" class="nav-link group">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2H6C4.89543 2 4 2.89543 4 4Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" />
                                            <path d="M8 13H16" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M8 17H12" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>
                                        <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">🧾 فواتير الطلبات</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        {{-- ====================================== --}}
                        {{-- قائمة الموظفين (account_code = 'emp') - لا نعرض تسجيل/سجل الحضور لمدير الشركة (لديه تقرير الحضور فقط تحت إدارة الشركة) --}}
                        {{-- ====================================== --}}
                        @if (Auth::user()->account_code == 'emp')
                            @php
                                $employee = \App\Models\Employee::where('user_id', Auth::user()->id)->first();
                                $todayAttendance = $employee
                                    ? \App\Models\Attendance::where('employee_id', $employee->id)
                                        ->whereDate('attendance_date', \Carbon\Carbon::today())
                                        ->first()
                                    : null;
                            @endphp

                            {{-- 2. الحضور والانصراف (للموظفين فقط - لا لمدير الشركة CM ولا لمدير الفرع BM) --}}
                            @if (Auth::user()->usertype_id != 'CM' && Auth::user()->usertype_id != 'BM')
                            <li class="menu nav-item">
                                <button type="button" class="nav-link group"
                                    :class="{ 'active': activeDropdown === 'employeeAttendance' }"
                                    @click="activeDropdown === 'employeeAttendance' ? activeDropdown = null : activeDropdown = 'employeeAttendance'">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="1.5" />
                                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span
                                            class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">⏰
                                            الحضور والانصراف</span>
                                        @if (!$todayAttendance)
                                            <span
                                                class="badge bg-warning rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">!</span>
                                        @elseif ($todayAttendance && !$todayAttendance->check_out_time)
                                            <span
                                                class="badge bg-info rounded-full px-2 py-0.5 text-xs ltr:ml-auto rtl:mr-auto">✓</span>
                                        @endif
                                    </div>
                                    <div class="rtl:rotate-180"
                                        :class="{ '!rotate-90': activeDropdown === 'employeeAttendance' }">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </button>
                                <ul x-cloak x-show="activeDropdown === 'employeeAttendance'" x-collapse
                                    class="sub-menu text-gray-500">
                                    <li>
                                        <a href="{{ $u('attendance') }}" class="flex items-center justify-between">
                                            <span>📍 تسجيل الحضور</span>
                                            @if (!$todayAttendance)
                                                <span class="badge bg-warning text-dark rounded-full px-2 text-xs">لم
                                                    تسجل</span>
                                            @elseif (!$todayAttendance->check_out_time)
                                                <span
                                                    class="badge bg-success text-white rounded-full px-2 text-xs">حاضر</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary text-white rounded-full px-2 text-xs">مكتمل</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><a href="{{ $u('attendance/my-history') }}">📅 سجل الحضور</a></li>
                                </ul>
                            </li>
                            @endif


                            {{-- 5. شحناتي (للسائقين فقط) --}}
                            @php
                                $driverShipmentsCount = $employee ? \App\Models\WorkShipment::where(function($q) use ($employee) {
                                    $q->where('mixer_driver_id', $employee->id)
                                      ->orWhere('truck_driver_id', $employee->id)
                                      ->orWhere('pump_driver_id', $employee->id);
                                })->whereNotIn('status', ['returned', 'cancelled'])->count() : 0;
                            @endphp
                            @if($driverShipmentsCount > 0 || ($employee && ($employee->job_title && (str_contains(strtolower($employee->job_title), 'سائق') || str_contains(strtolower($employee->job_title), 'driver')))))
                            <li class="menu nav-item">
                                <a href="{{ $u('driver/shipments') }}" class="nav-link group">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 text-gray-600 group-hover:!text-primary dark:text-[#506690]"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 7H16M8 11H12" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path
                                                d="M20 12V5.74853C20 5.0512 20 4.70254 19.8627 4.4337C19.7255 4.16487 19.4756 3.97811 18.9758 3.60459L16.7024 1.90779C16.3246 1.62555 16.1357 1.48443 15.917 1.42848C15.5903 1.34696 15.2447 1.42277 14.9855 1.62751L8.10778 6.8515C7.67893 7.17705 7.46451 7.33982 7.33232 7.5607C7.2 7.78158 7.2 8.0359 7.2 8.54455V9"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path
                                                d="M14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V10C2 6.22876 2 4.34315 3.17157 3.17157C4.34315 2 6.22876 2 10 2H14C17.7712 2 19.6569 2 20.8284 3.17157C22 4.34315 22 6.22876 22 10V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path d="M7 15L9 17L13 13" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span
                                            class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">🚚
                                            شحناتي</span>
                                        @if($driverShipmentsCount > 0)
                                            <span class="badge bg-primary rounded-full px-2 py-0.5 text-xs text-white ltr:ml-auto rtl:mr-auto">
                                                {{ $driverShipmentsCount }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            @endif

                        
                        @endif

                </div>
            </nav>
        </div>
