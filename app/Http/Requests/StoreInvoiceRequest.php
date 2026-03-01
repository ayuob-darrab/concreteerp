<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'invoice_type' => ['required', 'in:sales,purchase,credit_note,debit_note'],
            'account_id' => ['required', 'exists:contractor_accounts,id'],
            'work_order_id' => ['nullable', 'exists:work_orders,id'],
            'party_name' => ['required', 'string', 'max:255'],
            'party_phone' => ['nullable', 'string', 'max:20'],
            'party_address' => ['nullable', 'string', 'max:500'],
            'party_tax_number' => ['nullable', 'string', 'max:50'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'currency' => ['nullable', 'string', 'max:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0.0001'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['nullable', 'string', 'max:2000'],

            // بنود الفاتورة
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'string', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.work_order_item_id' => ['nullable', 'exists:work_order_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'invoice_type' => 'نوع الفاتورة',
            'account_id' => 'الحساب',
            'work_order_id' => 'طلب العمل',
            'party_name' => 'اسم الطرف',
            'party_phone' => 'هاتف الطرف',
            'party_address' => 'عنوان الطرف',
            'party_tax_number' => 'الرقم الضريبي',
            'invoice_date' => 'تاريخ الفاتورة',
            'due_date' => 'تاريخ الاستحقاق',
            'currency' => 'العملة',
            'exchange_rate' => 'سعر الصرف',
            'discount_percentage' => 'نسبة الخصم',
            'discount_amount' => 'مبلغ الخصم',
            'tax_percentage' => 'نسبة الضريبة',
            'notes' => 'ملاحظات',
            'terms' => 'الشروط والأحكام',
            'items' => 'بنود الفاتورة',
            'items.*.item_type' => 'نوع البند',
            'items.*.description' => 'وصف البند',
            'items.*.quantity' => 'الكمية',
            'items.*.unit' => 'الوحدة',
            'items.*.unit_price' => 'سعر الوحدة',
            'items.*.discount_percentage' => 'نسبة الخصم',
            'items.*.tax_percentage' => 'نسبة الضريبة',
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
            'after_or_equal' => ':attribute يجب أن يكون بعد أو يساوي تاريخ الفاتورة',
            'in' => 'قيمة :attribute غير صالحة',
            'array' => ':attribute يجب أن يكون مصفوفة',
            'items.min' => 'يجب إضافة بند واحد على الأقل',
        ];
    }
}
