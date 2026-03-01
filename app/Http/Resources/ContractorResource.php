<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractorResource extends JsonResource
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
            'code' => $this->code,
            'contractor_name' => $this->contractor_name,
            'contractor_type' => $this->contractor_type,
            'contractor_type_label' => $this->contractor_type === 'individual' ? 'فرد' : 'شركة',
            'classification' => $this->classification,
            'phone' => $this->phone,
            'phone_secondary' => $this->phone_secondary,
            'email' => $this->email,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            // البيانات المالية
            'credit_limit' => (float) $this->credit_limit,
            'current_balance' => $this->when($this->account, fn() => (float) $this->account->current_balance),
            'available_credit' => $this->when($this->account, fn() => (float) $this->available_credit),
            'is_over_credit_limit' => $this->when($this->account, fn() => $this->is_over_credit_limit),
            'payment_terms' => $this->payment_terms,
            'discount_percentage' => (float) $this->discount_percentage,

            // العنوان
            'address' => $this->address,
            'city' => $this->city,
            'region' => $this->region,

            // جهة الاتصال
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,

            // الإحصائيات
            'statistics' => [
                'total_orders' => $this->total_orders,
                'completed_orders' => $this->completed_orders,
                'cancelled_orders' => $this->cancelled_orders,
                'total_purchases' => (float) $this->total_purchases,
                'total_paid' => (float) $this->total_paid,
            ],

            // العلاقات
            'account' => new ContractorAccountResource($this->whenLoaded('account')),
            'branch' => $this->whenLoaded('branch', fn() => [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
            ]),

            // التواريخ
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
