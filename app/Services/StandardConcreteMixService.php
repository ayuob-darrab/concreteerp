<?php

namespace App\Services;

use App\Models\ConcreteMix;
use App\Models\ConcreteMixCategoryPrice;

class StandardConcreteMixService
{
    /**
     * خلطات الستاندرد في النظام: company_code = general وبدون فرع.
     */
    public static function generalTemplates()
    {
        return ConcreteMix::query()
            ->where('company_code', 'general')
            ->whereNull('branch_id')
            ->with('chemicals');
    }

    /**
     * نسخ خلطات general إلى مستوى الشركة (branch_id = null) لاستخدامها كقالب لكل الفروع.
     */
    public static function seedCompanyStandardMixes(string $companyCode): int
    {
        $created = 0;
        foreach (static::generalTemplates()->get() as $template) {
            if (static::companyLevelMixExists($companyCode, $template->classification, $template->notes)) {
                continue;
            }

            $mix = ConcreteMix::create([
                'classification' => $template->classification,
                'company_code' => $companyCode,
                'branch_id' => null,
                'cement' => $template->cement,
                'sand' => $template->sand,
                'gravel' => $template->gravel,
                'water' => $template->water,
                'notes' => $template->notes,
                'costPrice' => $template->costPrice ?? 0,
                'salePrice' => $template->salePrice ?? 0,
            ]);

            static::syncChemicalsFromTemplate($template, $mix);
            static::copyCategoryPricesToMix($template->id, $mix->id, $companyCode, false);
            $created++;
        }

        return $created;
    }

    /**
     * نسخ خلطات المستوى company (بدون فرع) إلى فرع مع ربطها بمواد المخزون الجديدة للفرع.
     * إذا لم توجد خلطات على مستوى الشركة، تُملأ أولاً من general ثم تُنسخ للفرع.
     */
    public static function seedBranchMixesFromCompanyTemplates(
        string $companyCode,
        int $branchId,
        string $cementCode,
        string $sandCode,
        string $gravelCode,
        string $waterCode
    ): int {
        $templates = ConcreteMix::query()
            ->where('company_code', $companyCode)
            ->whereNull('branch_id')
            ->with('chemicals')
            ->get();

        if ($templates->isEmpty()) {
            static::seedCompanyStandardMixes($companyCode);
            $templates = ConcreteMix::query()
                ->where('company_code', $companyCode)
                ->whereNull('branch_id')
                ->with('chemicals')
                ->get();
        }

        $created = 0;
        foreach ($templates as $item) {
            $exists = ConcreteMix::query()
                ->where('classification', $item->classification)
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('notes', $item->notes)
                ->exists();

            if ($exists) {
                continue;
            }

            $mix = ConcreteMix::create([
                'classification' => $item->classification,
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'cement' => $item->cement,
                'sand' => $item->sand,
                'gravel' => $item->gravel,
                'water' => $item->water,
                'notes' => $item->notes,
                'costPrice' => $item->costPrice ?? 0,
                'salePrice' => $item->salePrice ?? 0,
                'cement_code' => $cementCode,
                'sand_code' => $sandCode,
                'gravel_code' => $gravelCode,
                'water_code' => $waterCode,
            ]);

            static::syncChemicalsFromTemplate($item, $mix);
            static::copyCategoryPricesToMix($item->id, $mix->id, $companyCode, true);
            $created++;
        }

        return $created;
    }

    protected static function companyLevelMixExists(string $companyCode, string $classification, ?string $notes): bool
    {
        return ConcreteMix::query()
            ->where('company_code', $companyCode)
            ->whereNull('branch_id')
            ->where('classification', $classification)
            ->where('notes', $notes)
            ->exists();
    }

    protected static function syncChemicalsFromTemplate(ConcreteMix $template, ConcreteMix $target): void
    {
        foreach ($template->chemicals as $chem) {
            $target->chemicals()->attach($chem->id, [
                'quantity' => $chem->pivot->quantity ?? null,
            ]);
        }
    }

    /**
     * نسخ أسعار الفئات من خلطة مصدر إلى خلطة جديدة بكود الشركة الهدف.
     *
     * @param  bool  $onlyForCompanyCode  إن true يُقيَّد بـ company_code (نسخ من قالب الشركة إلى فرع).
     *                                      إن false تُنسخ كل صفوف concrete_mix_id (من قالب general).
     */
    protected static function copyCategoryPricesToMix(
        int $sourceMixId,
        int $targetMixId,
        string $companyCode,
        bool $onlyForCompanyCode
    ): void {
        $q = ConcreteMixCategoryPrice::query()->where('concrete_mix_id', $sourceMixId);
        if ($onlyForCompanyCode) {
            $q->where('company_code', $companyCode);
        }

        $rows = $q->get();

        foreach ($rows as $row) {
            ConcreteMixCategoryPrice::create([
                'company_code' => $companyCode,
                'concrete_mix_id' => $targetMixId,
                'pricing_category_id' => $row->pricing_category_id,
                'price_per_meter' => $row->price_per_meter,
                'cost_per_meter' => $row->cost_per_meter,
                'notes' => $row->notes,
                'is_active' => $row->is_active,
            ]);
        }
    }
}
