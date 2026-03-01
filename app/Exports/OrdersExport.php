<?php

namespace App\Exports;

use App\Services\Reports\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
        $report = ReportService::ordersReport(
            $this->companyCode,
            $this->branchId,
            $this->fromDate,
            $this->toDate,
            $this->filters
        );

        return collect($report['data'])->map(function ($order) {
            return [
                'order_number' => $order->order_number ?? '',
                'created_at' => $order->created_at ?? '',
                'customer_name' => $order->customer_name ?? '',
                'quantity' => $order->quantity ?? 0,
                'total_price' => $order->total_price ?? 0,
                'status' => $this->translateStatus($order->status ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم الطلب',
            'التاريخ',
            'العميل',
            'الكمية',
            'الإجمالي',
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
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمد',
            'in_progress' => 'جاري التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];
        return $statuses[$status] ?? $status;
    }
}
