<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $companyCode;
    protected $branchId;
    protected $filters;

    public function __construct(string $companyCode, ?int $branchId = null, array $filters = [])
    {
        $this->companyCode = $companyCode;
        $this->branchId = $branchId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $report = ReportService::inventoryReport(
            $this->companyCode,
            $this->branchId,
            $this->filters
        );

        return collect($report['data'])->map(function ($material) {
            return [
                'name' => $material->name ?? '',
                'current_quantity' => $material->current_quantity ?? 0,
                'min_quantity' => $material->min_quantity ?? 0,
                'unit' => $material->unit ?? '',
                'unit_price' => $material->unit_price ?? 0,
                'total_value' => ($material->current_quantity ?? 0) * ($material->unit_price ?? 0),
                'status' => ($material->current_quantity ?? 0) <= ($material->min_quantity ?? 0) ? 'منخفض' : 'طبيعي',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'اسم المادة',
            'الكمية الحالية',
            'الحد الأدنى',
            'الوحدة',
            'سعر الوحدة',
            'القيمة الإجمالية',
            'الحالة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }
}
