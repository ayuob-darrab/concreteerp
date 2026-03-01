<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'invoice_type' => $this->invoice_type,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            // بيانات الطرف
            'party_name' => $this->party_name,
            'party_phone' => $this->party_phone,
            'party_address' => $this->party_address,
            'party_tax_number' => $this->party_tax_number,

            // التواريخ
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'is_overdue' => $this->is_overdue,
            'days_overdue' => $this->days_overdue,

            // المبالغ
            'subtotal' => (float) $this->subtotal,
            'discount_percentage' => (float) $this->discount_percentage,
            'discount_amount' => (float) $this->discount_amount,
            'tax_percentage' => (float) $this->tax_percentage,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'payment_percentage' => $this->payment_percentage,
            'currency' => $this->currency ?? 'SAR',

            // البنود
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenCounted('items'),

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
            'work_order' => $this->whenLoaded('workOrder', fn() => [
                'id' => $this->workOrder->id,
                'order_number' => $this->workOrder->order_number ?? $this->workOrder->id,
            ]),
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),

            'notes' => $this->notes,
            'terms' => $this->terms,

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
