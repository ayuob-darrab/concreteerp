<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $report = ReportService::companiesReport($this->filters);

        return collect($report['data'])->map(function ($company) {
            return [
                'company_code' => $company->company_code ?? '',
                'name' => $company->name ?? '',
                'branches_count' => $company->branches_count ?? 0,
                'users_count' => $company->users_count ?? 0,
                'status' => $this->translateStatus($company->status ?? ''),
                'created_at' => $company->created_at ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'كود الشركة',
            'اسم الشركة',
            'عدد الفروع',
            'عدد المستخدمين',
            'الحالة',
            'تاريخ التسجيل',
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
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];
        return $statuses[$status] ?? $status;
    }
}
