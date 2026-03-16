<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractorRequest extends FormRequest
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
            // البيانات الأساسية
            'contractor_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'contractor_type' => ['required', 'in:individual,company'],
            'classification' => ['nullable', 'in:A,B,C,D'],

            // بيانات الهوية/الشركة
            'id_type' => ['nullable', 'in:national_id,passport,commercial_register,other'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'id_expiry_date' => ['nullable', 'date', 'after:today'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'commercial_register' => ['nullable', 'string', 'max:50'],
            'cr_expiry_date' => ['nullable', 'date', 'after:today'],

            // العنوان
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'gps_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // الإعدادات المالية
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'integer', 'min:0', 'max:365'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency' => ['nullable', 'string', 'max:3'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_iban' => ['nullable', 'string', 'max:50'],

            // المعلومات الإضافية
            'contact_person' => ['nullable', 'string', 'max:100'],
            'contact_position' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'contractor_name' => 'اسم المقاول',
            'phone' => 'رقم الهاتف',
            'phone_secondary' => 'رقم الهاتف الثانوي',
            'email' => 'البريد الإلكتروني',
            'contractor_type' => 'نوع المقاول',
            'classification' => 'التصنيف',
            'id_type' => 'نوع الهوية',
            'id_number' => 'رقم الهوية',
            'id_expiry_date' => 'تاريخ انتهاء الهوية',
            'tax_number' => 'الرقم الضريبي',
            'commercial_register' => 'السجل التجاري',
            'cr_expiry_date' => 'تاريخ انتهاء السجل',
            'address' => 'العنوان',
            'city' => 'المحافظة',
            'region' => 'المنطقة',
            'postal_code' => 'الرمز البريدي',
            'country' => 'الدولة',
            'credit_limit' => 'الحد الائتماني',
            'payment_terms' => 'مدة السداد',
            'discount_percentage' => 'نسبة الخصم',
            'currency' => 'العملة',
            'bank_name' => 'اسم البنك',
            'bank_account_number' => 'رقم الحساب',
            'bank_iban' => 'رقم الآيبان',
            'contact_person' => 'جهة الاتصال',
            'contact_position' => 'المنصب',
            'contact_phone' => 'هاتف جهة الاتصال',
            'contact_email' => 'بريد جهة الاتصال',
            'notes' => 'ملاحظات',
            'tags' => 'الوسوم',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'string' => 'حقل :attribute يجب أن يكون نصاً',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرف',
            'email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'date' => 'صيغة التاريخ غير صحيحة',
            'after' => ':attribute يجب أن يكون بعد اليوم',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً',
            'min' => 'حقل :attribute يجب أن يكون على الأقل :min',
            'between' => 'حقل :attribute يجب أن يكون بين :min و :max',
            'in' => 'قيمة :attribute غير صالحة',
        ];
    }
}
