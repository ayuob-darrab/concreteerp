<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdvancesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $companyCode;
    protected $branchId;
    protected $fromDate;
    protected $toDate;
    protected $filters;

    public function __construct(string $companyCode, ?int $branchId = null, ?string $fromDate = null, ?string $toDate = null, array $filters = [])
    {
        $this->companyCode = $companyCode;
        $this->branchId = $branchId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->filters = $filters;
    }

    public function collection()
    {
        $report = ReportService::advancesReport(
            $this->companyCode,
            $this->branchId,
            $this->fromDate,
            $this->toDate,
            $this->filters
        );

        return collect($report['data'])->map(function ($advance) {
            return [
                'advance_number' => $advance->advance_number ?? '',
                'created_at' => $advance->created_at ?? '',
                'employee_name' => $advance->employee_name ?? '',
                'amount' => $advance->amount ?? 0,
                'remaining_amount' => $advance->remaining_amount ?? 0,
                'status' => $this->translateStatus($advance->status ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم السلفة',
            'التاريخ',
            'الموظف',
            'المبلغ',
            'المتبقي',
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

    protected function translateStatus($status): string
    {
        $statuses = [
            'active' => 'نشطة',
            'paid' => 'مسددة',
            'cancelled' => 'ملغاة',
        ];
        return $statuses[$status] ?? $status;
    }
}
