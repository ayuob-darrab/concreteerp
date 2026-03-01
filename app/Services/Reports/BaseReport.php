<?php

namespace App\Services\Reports;

use Illuminate\Support\Carbon;

abstract class BaseReport
{
    protected $companyCode;
    protected $branchId;
    protected $dateFrom;
    protected $dateTo;
    protected $filters = [];

    public function __construct(?string $companyCode = null, ?int $branchId = null)
    {
        $this->companyCode = $companyCode;
        $this->branchId = $branchId;
    }

    /**
     * تعيين نطاق التاريخ
     */
    public function setDateRange($from, $to): self
    {
        $this->dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfMonth();
        $this->dateTo = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
        return $this;
    }

    /**
     * تعيين الفلاتر
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * تعيين فلتر الفترة الجاهزة
     */
    public function setPreset(string $preset): self
    {
        [$from, $to] = self::getPresetDates($preset);
        return $this->setDateRange($from, $to);
    }

    /**
     * الحصول على تواريخ الفترة الجاهزة
     */
    public static function getPresetDates(string $preset): array
    {
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'yesterday':
                return [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'last_week':
                return [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'last_month':
                return [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()];
            case 'this_quarter':
                return [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()];
            case 'last_quarter':
                return [$now->copy()->subQuarter()->startOfQuarter(), $now->copy()->subQuarter()->endOfQuarter()];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            case 'last_year':
                return [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()];
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfDay()];
        }
    }

    /**
     * الفترات الجاهزة
     */
    public static function getPresets(): array
    {
        return [
            'today' => 'اليوم',
            'yesterday' => 'أمس',
            'this_week' => 'هذا الأسبوع',
            'last_week' => 'الأسبوع الماضي',
            'this_month' => 'هذا الشهر',
            'last_month' => 'الشهر الماضي',
            'this_quarter' => 'هذا الربع',
            'last_quarter' => 'الربع الماضي',
            'this_year' => 'هذه السنة',
            'last_year' => 'السنة الماضية',
            'custom' => 'فترة مخصصة',
        ];
    }

    /**
     * توليد التقرير
     */
    abstract public function generate(): array;

    /**
     * عنوان التقرير
     */
    abstract public function getTitle(): string;

    /**
     * أعمدة التقرير
     */
    abstract public function getColumns(): array;

    /**
     * معلومات التقرير
     */
    public function getMeta(): array
    {
        return [
            'title' => $this->getTitle(),
            'date_from' => $this->dateFrom?->format('Y-m-d'),
            'date_to' => $this->dateTo?->format('Y-m-d'),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'company_code' => $this->companyCode,
            'branch_id' => $this->branchId,
            'filters' => $this->filters,
        ];
    }
}
