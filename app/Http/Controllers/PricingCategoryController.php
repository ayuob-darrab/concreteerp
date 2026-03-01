<?php

namespace App\Http\Controllers;

use App\Models\PricingCategory;
use App\Models\ConcreteMix;
use App\Models\ConcreteMixCategoryPrice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PricingCategoryController extends Controller
{
    /**
     * عرض قائمة الفئات السعرية (للسوبر أدمن)
     */
    public function index()
    {
        // التحقق من صلاحيات السوبر أدمن
        if (Auth::user()->usertype_id !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
        }

        $categories = PricingCategory::ordered()->get();

        return view('pricing_categories.index', compact('categories'));
    }

    /**
     * حفظ فئة سعرية جديدة
     */
    public function store(Request $request)
    {
        // التحقق من صلاحيات السوبر أدمن
        if (Auth::user()->usertype_id !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:pricing_categories,name',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique' => 'اسم الفئة موجود مسبقاً',
        ]);

        $category = PricingCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إضافة الفئة السعرية بنجاح');
    }

    /**
     * تحديث فئة سعرية
     */
    public function update(Request $request, $id)
    {
        // التحقق من صلاحيات السوبر أدمن
        if (Auth::user()->usertype_id !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:pricing_categories,name,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = PricingCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'تم تحديث الفئة السعرية بنجاح');
    }

    /**
     * حذف فئة سعرية
     */
    public function destroy($id)
    {
        // التحقق من صلاحيات السوبر أدمن
        if (Auth::user()->usertype_id !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية');
        }

        $category = PricingCategory::findOrFail($id);

        // التحقق من عدم وجود أسعار مرتبطة
        if ($category->mixPrices()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الفئة لوجود أسعار مرتبطة بها');
        }

        $category->delete();

        return back()->with('success', 'تم حذف الفئة السعرية بنجاح');
    }

    /**
     * تغيير حالة الفئة (نشطة/غير نشطة)
     */
    public function toggleStatus($id)
    {
        // التحقق من صلاحيات السوبر أدمن
        if (Auth::user()->usertype_id !== 'SA') {
            return redirect('/')->with('error', 'ليس لديك صلاحية');
        }

        $category = PricingCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$status} الفئة السعرية بنجاح");
    }

    // ================================================
    // أسعار الشركات - يستخدمها مدير الشركة
    // ================================================

    /**
     * عرض أسعار الخلطات للشركة الحالية
     */
    public function companyPrices()
    {
        $companyCode = Auth::user()->company_code;

        // الخلطات الخاصة بالفروع فقط (التي لها branch_id) مع علاقات المواد
        $mixes = ConcreteMix::where('company_code', $companyCode)
            ->whereNotNull('branch_id')
            ->with(['branchName', 'cementInventory', 'sandInventory', 'gravelInventory', 'waterInventory', 'chemicals'])
            ->get();

        // حساب تكلفة كل خلطة
        foreach ($mixes as $mix) {
            $mix->calculated_cost = $this->calculateMixCost($mix);
        }

        // الفئات السعرية النشطة
        $categories = PricingCategory::active()->ordered()->get();

        // الأسعار الحالية
        $prices = ConcreteMixCategoryPrice::where('company_code', $companyCode)
            ->with(['concreteMix', 'pricingCategory'])
            ->get()
            ->groupBy('concrete_mix_id');

        return view('pricing_categories.company_prices', compact('mixes', 'categories', 'prices'));
    }

    /**
     * حساب تكلفة الخلطة الخرسانية
     */
    private function calculateMixCost($mix)
    {
        $cost = 0;

        // تكلفة الاسمنت (بالكيس - كل طن = 20 كيس)
        if ($mix->cementInventory && $mix->cement > 0) {
            // cement محفوظ بالكيلوغرام، كل كيس = 50 كغم
            $bags = $mix->cement / 50; // عدد الأكياس
            $cost += $bags * $mix->cementInventory->unit_cost;
        }

        // تكلفة الرمل (بالمتر المكعب)
        if ($mix->sandInventory && $mix->sand > 0) {
            $cost += $mix->sand * $mix->sandInventory->unit_cost;
        }

        // تكلفة الحصى (بالمتر المكعب)
        if ($mix->gravelInventory && $mix->gravel > 0) {
            $cost += $mix->gravel * $mix->gravelInventory->unit_cost;
        }

        // تكلفة المياه (باللتر)
        if ($mix->waterInventory && $mix->water > 0) {
            $cost += $mix->water * $mix->waterInventory->unit_cost;
        }

        // تكلفة المواد الكيميائية
        foreach ($mix->chemicals as $chemical) {
            if ($chemical->pivot->quantity > 0 && $chemical->unit_cost > 0) {
                $cost += $chemical->pivot->quantity * $chemical->unit_cost;
            }
        }

        return round($cost, 0);
    }

    /**
     * حفظ/تحديث أسعار الخلطات للشركة
     */
    public function saveCompanyPrices(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        $request->validate([
            'prices' => 'required|array',
            'prices.*.mix_id' => 'required|exists:concrete_mixes,id',
            'prices.*.category_id' => 'required|exists:pricing_categories,id',
            'prices.*.price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->prices as $priceData) {
                // حفظ فقط إذا كان السعر موجود
                if (!empty($priceData['price']) && $priceData['price'] > 0) {
                    ConcreteMixCategoryPrice::updateOrCreate(
                        [
                            'company_code' => $companyCode,
                            'concrete_mix_id' => $priceData['mix_id'],
                            'pricing_category_id' => $priceData['category_id'],
                        ],
                        [
                            'price_per_meter' => $priceData['price'],
                            'cost_per_meter' => $priceData['cost'] ?? null,
                            'notes' => $priceData['notes'] ?? null,
                            'is_active' => true,
                        ]
                    );
                }
            }

            DB::commit();

            // إذا كان الطلب AJAX
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'تم حفظ الأسعار بنجاح']);
            }

            return back()->with('success', 'تم حفظ الأسعار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حفظ الأسعار: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'حدث خطأ أثناء حفظ الأسعار: ' . $e->getMessage());
        }
    }

    /**
     * حفظ سعر واحد (AJAX)
     */
    public function saveSinglePrice(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        $request->validate([
            'mix_id' => 'required|exists:concrete_mixes,id',
            'category_id' => 'required|exists:pricing_categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $price = ConcreteMixCategoryPrice::updateOrCreate(
                [
                    'company_code' => $companyCode,
                    'concrete_mix_id' => $request->mix_id,
                    'pricing_category_id' => $request->category_id,
                ],
                [
                    'price_per_meter' => $request->price,
                    'cost_per_meter' => $request->cost ?? null,
                    'is_active' => true,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ السعر بنجاح',
                'price' => $price
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على أسعار خلطة معينة (AJAX)
     */
    public function getMixPrices($mixId)
    {
        $companyCode = Auth::user()->company_code;

        $prices = ConcreteMixCategoryPrice::where('company_code', $companyCode)
            ->where('concrete_mix_id', $mixId)
            ->with('pricingCategory')
            ->get();

        return response()->json([
            'success' => true,
            'prices' => $prices
        ]);
    }

    /**
     * الحصول على تفاصيل تكلفة خلطة معينة (AJAX)
     */
    public function getCostDetails($mixId)
    {
        $companyCode = Auth::user()->company_code;

        $mix = ConcreteMix::where('id', $mixId)
            ->where('company_code', $companyCode)
            ->with(['cementInventory', 'sandInventory', 'gravelInventory', 'waterInventory', 'chemicals'])
            ->first();

        if (!$mix) {
            return response()->json(['success' => false, 'message' => 'الخلطة غير موجودة'], 404);
        }

        $details = [];
        $totalCost = 0;

        // تكلفة الاسمنت
        if ($mix->cementInventory && $mix->cement > 0) {
            $bags = $mix->cement / 50; // كل كيس 50 كغم
            $cost = $bags * $mix->cementInventory->unit_cost;
            $details[] = [
                'material' => 'اسمنت',
                'quantity' => $mix->cement . ' كغم (' . round($bags, 2) . ' كيس)',
                'unit_cost' => number_format($mix->cementInventory->unit_cost, 0) . ' دينار/كيس',
                'total' => number_format($cost, 0) . ' دينار',
                'total_raw' => $cost
            ];
            $totalCost += $cost;
        }

        // تكلفة الرمل
        if ($mix->sandInventory && $mix->sand > 0) {
            $cost = $mix->sand * $mix->sandInventory->unit_cost;
            $details[] = [
                'material' => 'رمل',
                'quantity' => $mix->sand . ' م³',
                'unit_cost' => number_format($mix->sandInventory->unit_cost, 0) . ' دينار/م³',
                'total' => number_format($cost, 0) . ' دينار',
                'total_raw' => $cost
            ];
            $totalCost += $cost;
        }

        // تكلفة الحصى
        if ($mix->gravelInventory && $mix->gravel > 0) {
            $cost = $mix->gravel * $mix->gravelInventory->unit_cost;
            $details[] = [
                'material' => 'حصى',
                'quantity' => $mix->gravel . ' م³',
                'unit_cost' => number_format($mix->gravelInventory->unit_cost, 0) . ' دينار/م³',
                'total' => number_format($cost, 0) . ' دينار',
                'total_raw' => $cost
            ];
            $totalCost += $cost;
        }

        // تكلفة المياه
        if ($mix->waterInventory && $mix->water > 0) {
            $cost = $mix->water * $mix->waterInventory->unit_cost;
            $details[] = [
                'material' => 'مياه',
                'quantity' => $mix->water . ' لتر',
                'unit_cost' => number_format($mix->waterInventory->unit_cost, 0) . ' دينار/لتر',
                'total' => number_format($cost, 0) . ' دينار',
                'total_raw' => $cost
            ];
            $totalCost += $cost;
        }

        // تكلفة المواد الكيميائية
        foreach ($mix->chemicals as $chemical) {
            if ($chemical->pivot->quantity > 0 && $chemical->unit_cost > 0) {
                $cost = $chemical->pivot->quantity * $chemical->unit_cost;
                $details[] = [
                    'material' => $chemical->name,
                    'quantity' => $chemical->pivot->quantity . ' ' . $chemical->unit,
                    'unit_cost' => number_format($chemical->unit_cost, 0) . ' دينار/' . $chemical->unit,
                    'total' => number_format($cost, 0) . ' دينار',
                    'total_raw' => $cost
                ];
                $totalCost += $cost;
            }
        }

        return response()->json([
            'success' => true,
            'mix' => $mix->classification,
            'details' => $details,
            'total_cost' => number_format($totalCost, 0),
            'total_cost_raw' => $totalCost
        ]);
    }
}
