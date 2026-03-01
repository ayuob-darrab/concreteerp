<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckResource extends JsonResource
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
            'check_number' => $this->check_number,
            'check_type' => $this->check_type,
            'type_label' => $this->type_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            // بيانات البنك
            'bank_name' => $this->bank_name,
            'bank_branch' => $this->bank_branch,
            'bank_account_number' => $this->bank_account_number,

            // الأطراف
            'drawer_name' => $this->drawer_name,
            'drawer_id_number' => $this->drawer_id_number,
            'beneficiary_name' => $this->beneficiary_name,
            'current_holder' => $this->current_holder,

            // المبلغ
            'amount' => (float) $this->amount,
            'currency' => $this->currency ?? 'SAR',

            // التواريخ
            'issue_date' => $this->issue_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'deposit_date' => $this->deposit_date?->format('Y-m-d'),
            'collection_date' => $this->collection_date?->format('Y-m-d'),
            'is_overdue' => $this->is_overdue,
            'days_until_due' => $this->days_until_due,

            // الرفض
            'rejection_reason' => $this->rejection_reason,
            'rejection_count' => $this->rejection_count,

            // التظهيرات
            'endorsements' => $this->endorsements,

            // الصور
            'image_front' => $this->image_front ? asset('storage/' . $this->image_front) : null,
            'image_back' => $this->image_back ? asset('storage/' . $this->image_back) : null,

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
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),

            // سجل الحالات
            'status_logs' => $this->whenLoaded(
                'statusLogs',
                fn() =>
                $this->statusLogs->map(fn($log) => [
                    'status' => $log->status,
                    'status_label' => $log->status_label,
                    'notes' => $log->notes,
                    'created_by' => $log->creator?->name,
                    'created_at' => $log->created_at->toISOString(),
                ])
            ),

            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
