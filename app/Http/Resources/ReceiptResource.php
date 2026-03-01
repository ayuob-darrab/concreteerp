<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'receipt_type' => $this->receipt_type,
            'type_label' => $this->type_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            // بيانات الطرف
            'party_name' => $this->party_name,

            // المبلغ
            'amount' => (float) $this->amount,
            'currency' => $this->currency ?? 'SAR',
            'exchange_rate' => (float) $this->exchange_rate,

            // طريقة الدفع
            'payment_method' => $this->payment_method,
            'payment_method_label' => $this->payment_method_label,
            'payment_reference' => $this->payment_reference,
            'bank_name' => $this->bank_name,

            // التاريخ
            'receipt_date' => $this->receipt_date->format('Y-m-d'),

            // العلاقات
            'account' => $this->whenLoaded('account', fn() => [
                'id' => $this->account->id,
                'account_number' => $this->account->account_number,
                'contractor' => $this->when($this->account->contractor, [
                    'id' => $this->account->contractor->id,
                    'code' => $this->account->contractor->code,
                    'name' => $this->account->contractor->contractor_name,
                ]),
            ]),
            'invoice' => $this->whenLoaded('invoice', fn() => [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
            ]),
            'check' => $this->whenLoaded('check', fn() => [
                'id' => $this->check->id,
                'check_number' => $this->check->check_number,
            ]),
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'approver' => $this->whenLoaded('approver', fn() => [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ]),

            'description' => $this->description,
            'notes' => $this->notes,

            // بيانات الإلغاء
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),

            'approved_at' => $this->approved_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
