<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Invoice::with(['contractor', 'createdBy'])
            ->where('company_id', auth()->user()->company_id);

        // تطبيق الفلاتر
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['contractor_id'])) {
            $query->where('contractor_id', $this->filters['contractor_id']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhereHas('contractor', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->filters['search'] . '%');
                    });
            });
        }

        return $query->orderBy('invoice_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'تاريخ الفاتورة',
            'اسم المقاول',
            'المبلغ الإجمالي',
            'الخصم',
            'الضريبة',
            'الصافي',
            'المدفوع',
            'المتبقي',
            'تاريخ الاستحقاق',
            'الحالة',
            'الموظف',
        ];
    }

    public function map($invoice): array
    {
        $statuses = [
            'draft' => 'مسودة',
            'issued' => 'صادرة',
            'paid' => 'مدفوعة',
            'partially_paid' => 'مدفوعة جزئياً',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغاة',
        ];

        return [
            $invoice->invoice_number,
            $invoice->invoice_date?->format('Y-m-d'),
            $invoice->contractor?->name,
            number_format($invoice->subtotal, 2),
            number_format($invoice->discount_amount, 2),
            number_format($invoice->tax_amount, 2),
            number_format($invoice->total_amount, 2),
            number_format($invoice->paid_amount, 2),
            number_format($invoice->remaining_amount, 2),
            $invoice->due_date?->format('Y-m-d'),
            $statuses[$invoice->status] ?? $invoice->status,
            $invoice->createdBy?->name,
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
                'startColor' => ['rgb' => '28A745'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // تنسيق الأعمدة الرقمية
        $sheet->getStyle('D:I')->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // RTL للغة العربية
        $sheet->setRightToLeft(true);

        return [];
    }
}
