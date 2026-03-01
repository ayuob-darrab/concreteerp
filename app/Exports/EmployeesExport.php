<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
        $report = ReportService::employeesReport(
            $this->companyCode,
            $this->branchId,
            $this->filters
        );

        return collect($report['data'])->map(function ($employee) {
            return [
                'employee_number' => $employee->employee_number ?? '',
                'name' => $employee->name ?? '',
                'department' => $employee->department ?? '',
                'job_title' => $employee->job_title ?? '',
                'basic_salary' => $employee->basic_salary ?? 0,
                'status' => $this->translateStatus($employee->status ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم الموظف',
            'الاسم',
            'القسم',
            'المسمى الوظيفي',
            'الراتب الأساسي',
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
        return $status === 'active' ? 'نشط' : 'غير نشط';
    }
}
