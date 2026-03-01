<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Receipt;

class StorePaymentRequest extends FormRequest
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
            'receipt_type' => ['required', 'in:receipt,payment'],
            'account_id' => ['required', 'exists:contractor_accounts,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'check_id' => ['nullable', 'exists:checks,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0.0001'],
            'payment_method' => ['required', 'in:cash,bank_transfer,check,credit_card,mobile_payment'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['required_if:payment_method,bank_transfer,check', 'nullable', 'string', 'max:100'],
            'receipt_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],

            // بيانات الشيك إذا كانت طريقة الدفع شيك
            'check_number' => ['required_if:payment_method,check', 'nullable', 'string', 'max:50'],
            'check_due_date' => ['required_if:payment_method,check', 'nullable', 'date'],
            'drawer_name' => ['required_if:payment_method,check', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'receipt_type' => 'نوع السند',
            'account_id' => 'الحساب',
            'invoice_id' => 'الفاتورة',
            'check_id' => 'الشيك',
            'amount' => 'المبلغ',
            'currency' => 'العملة',
            'exchange_rate' => 'سعر الصرف',
            'payment_method' => 'طريقة الدفع',
            'payment_reference' => 'رقم المرجع',
            'bank_name' => 'اسم البنك',
            'receipt_date' => 'تاريخ السند',
            'description' => 'الوصف',
            'notes' => 'ملاحظات',
            'check_number' => 'رقم الشيك',
            'check_due_date' => 'تاريخ استحقاق الشيك',
            'drawer_name' => 'اسم الساحب',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'required_if' => 'حقل :attribute مطلوب',
            'exists' => ':attribute غير موجود',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً',
            'min' => 'حقل :attribute يجب أن يكون على الأقل :min',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max',
            'date' => 'صيغة التاريخ غير صحيحة',
            'in' => 'قيمة :attribute غير صالحة',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تحديد نوع السند تلقائياً إذا لم يحدد
        if (!$this->has('receipt_type')) {
            $this->merge([
                'receipt_type' => Receipt::TYPE_RECEIPT,
            ]);
        }
    }
}
