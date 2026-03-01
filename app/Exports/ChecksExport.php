<?php

namespace App\Exports;

use App\Models\Check;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChecksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Check::with(['contractor', 'drawer', 'receivedBy'])
            ->where('company_id', auth()->user()->company_id);

        // تطبيق الفلاتر
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (!empty($this->filters['contractor_id'])) {
            $query->where('contractor_id', $this->filters['contractor_id']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('due_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('due_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['bank'])) {
            $query->where('bank_name', 'like', '%' . $this->filters['bank'] . '%');
        }

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('check_number', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhereHas('contractor', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->filters['search'] . '%');
                    });
            });
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'رقم الشيك',
            'النوع',
            'اسم المقاول',
            'المبلغ',
            'البنك',
            'رقم الحساب',
            'تاريخ الإصدار',
            'تاريخ الاستحقاق',
            'الساحب',
            'الحالة',
            'ملاحظات',
            'المستلم',
        ];
    }

    public function map($check): array
    {
        $types = [
            'incoming' => 'وارد',
            'outgoing' => 'صادر',
        ];

        $statuses = [
            'pending' => 'قيد الانتظار',
            'deposited' => 'مودع',
            'collected' => 'محصل',
            'rejected' => 'مرفوض',
            'returned' => 'مرتجع',
            'cancelled' => 'ملغي',
            'endorsed' => 'مظهر',
        ];

        return [
            $check->check_number,
            $types[$check->type] ?? $check->type,
            $check->contractor?->name,
            number_format($check->amount, 2),
            $check->bank_name,
            $check->account_number,
            $check->issue_date?->format('Y-m-d'),
            $check->due_date?->format('Y-m-d'),
            $check->drawer_name,
            $statuses[$check->status] ?? $check->status,
            $check->notes,
            $check->receivedBy?->name,
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
                'startColor' => ['rgb' => 'FFC107'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // تنسيق العمود الرقمي
        $sheet->getStyle('D')->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // RTL للغة العربية
        $sheet->setRightToLeft(true);

        return [];
    }
}
