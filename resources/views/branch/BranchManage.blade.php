@extends('layouts.app')

@section('page-title', 'ادارة فرع : ' . auth()->user()->branchName->branch_name)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">ادارة الفرع</h5>
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">

                <!-- حسابات الفرع -->
                <a href="/ConcreteERP/accounts/listBranchaccounts">
                    <button type="button"
                        class="btn btn-outline-primary rounded-full w-full py-4 text-lg">المستخدمين</button>
                </a>

                <!-- موظفين الفرع -->
                <a href="/ConcreteERP/Employees/listBranchemployees">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        موظفين الفرع
                    </button>
                </a>

                <!-- سيارات الفرع -->
                <a href="/ConcreteERP/cars/ListBranchCar">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        سيارات الفرع
                    </button>
                </a>

                <!-- مواد الكونكريت -->
                <a href="/ConcreteERP/warehouse/BranchConcreteMix">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        مواد الكونكريت
                    </button>
                </a>

                <!-- المواد الكيميائية -->
                <a href="/ConcreteERP/warehouse/Branchlistchemicals">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        المواد الكيميائية
                    </button>
                </a>

                <!-- المواد الأساسية -->
                <a href="/ConcreteERP/warehouse/addMainMaterialsBranch">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        المواد الأساسية
                    </button>
                </a>
                <a href="/ConcreteERP/car-types">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        أنواع السيارات
                    </button>
                </a>

                <!-- الصيانة -->
                <a href="/ConcreteERP/car-maintenance">
                    <button type="button" class="btn btn-outline-primary rounded-full w-full py-4 text-lg">
                        🔧 الصيانة
                    </button>
                </a>



            </div>


        </div>

    </div>
@endsection
