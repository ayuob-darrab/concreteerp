<?php

namespace App\Exports;

use App\Models\Contractor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Contractor::with(['account'])
            ->where('company_id', auth()->user()->company_id);

        // تطبيق الفلاتر
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'active') {
                $query->where('is_blocked', false);
            } elseif ($this->filters['status'] === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        if (!empty($this->filters['classification'])) {
            $query->where('classification', $this->filters['classification']);
        }

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('code', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'كود المقاول',
            'الاسم',
            'الهاتف',
            'البريد الإلكتروني',
            'العنوان',
            'السجل التجاري',
            'الرقم الضريبي',
            'التصنيف',
            'الحالة',
            'الرصيد',
            'حد الائتمان',
            'تاريخ التسجيل',
        ];
    }

    public function map($contractor): array
    {
        $classifications = [
            'A' => 'A - ممتاز',
            'B' => 'B - جيد',
            'C' => 'C - متوسط',
            'D' => 'D - ضعيف',
        ];

        return [
            $contractor->code,
            $contractor->name,
            $contractor->phone,
            $contractor->email,
            $contractor->address,
            $contractor->commercial_register,
            $contractor->tax_number,
            $classifications[$contractor->classification] ?? $contractor->classification,
            $contractor->is_blocked ? 'محظور' : 'نشط',
            number_format($contractor->account?->balance ?? 0, 2),
            number_format($contractor->credit_limit ?? 0, 2),
            $contractor->created_at?->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // تنسيق الرأس
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A90D9'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // تنسيق الجدول
        $sheet->getStyle('A:L')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
        );

        // RTL للغة العربية
        $sheet->setRightToLeft(true);

        return [];
    }
}
