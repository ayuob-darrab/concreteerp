<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Check;

class StoreCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'check_type' => ['required', 'in:incoming,outgoing'],
            'account_id' => ['required', 'exists:contractor_accounts,id'],
            'check_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_branch' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'drawer_name' => ['required', 'string', 'max:255'],
            'drawer_id_number' => ['nullable', 'string', 'max:50'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0.0001'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'notes' => ['nullable', 'string', 'max:1000'],

            // صور الشيك
            'image_front' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'image_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'check_type' => 'نوع الشيك',
            'account_id' => 'الحساب',
            'check_number' => 'رقم الشيك',
            'bank_name' => 'اسم البنك',
            'bank_branch' => 'فرع البنك',
            'bank_account_number' => 'رقم الحساب البنكي',
            'drawer_name' => 'اسم الساحب',
            'drawer_id_number' => 'رقم هوية الساحب',
            'beneficiary_name' => 'اسم المستفيد',
            'amount' => 'المبلغ',
            'currency' => 'العملة',
            'exchange_rate' => 'سعر الصرف',
            'issue_date' => 'تاريخ الإصدار',
            'due_date' => 'تاريخ الاستحقاق',
            'invoice_id' => 'الفاتورة',
            'notes' => 'ملاحظات',
            'image_front' => 'صورة الشيك الأمامية',
            'image_back' => 'صورة الشيك الخلفية',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'exists' => ':attribute غير موجود',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً',
            'min' => 'حقل :attribute يجب أن يكون على الأقل :min',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max',
            'date' => 'صيغة التاريخ غير صحيحة',
            'after_or_equal' => ':attribute يجب أن يكون بعد أو يساوي تاريخ الإصدار',
            'in' => 'قيمة :attribute غير صالحة',
            'image' => ':attribute يجب أن يكون صورة',
            'mimes' => ':attribute يجب أن يكون من نوع: jpg, jpeg, png',
        ];
    }
}
