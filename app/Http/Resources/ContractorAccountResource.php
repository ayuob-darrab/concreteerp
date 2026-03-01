<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractorAccountResource extends JsonResource
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
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'account_type' => $this->account_type,
            'credit_limit' => (float) $this->credit_limit,
            'current_balance' => (float) $this->current_balance,
            'available_credit' => (float) $this->available_credit,
            'is_over_limit' => $this->is_over_limit,
            'currency' => $this->currency,
            'is_active' => $this->is_active,

            // العلاقات
            'contractor' => $this->whenLoaded('contractor', fn() => [
                'id' => $this->contractor->id,
                'code' => $this->contractor->code,
                'name' => $this->contractor->contractor_name,
            ]),

            // آخر المعاملات
            'recent_transactions' => $this->whenLoaded(
                'transactions',
                fn() =>
                $this->transactions->take(5)->map(fn($t) => [
                    'id' => $t->id,
                    'type' => $t->transaction_type,
                    'amount' => (float) $t->amount,
                    'description' => $t->description,
                    'date' => $t->created_at->toISOString(),
                ])
            ),

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
