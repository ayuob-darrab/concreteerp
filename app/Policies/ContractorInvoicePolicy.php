<?php

namespace App\Policies;

use App\Models\ContractorInvoice;
use App\Models\User;

class ContractorInvoicePolicy
{
    public function view(User $user, ContractorInvoice $invoice): bool
    {
        if ($user->company_code !== $invoice->company_code) {
            return false;
        }

        // المقاول: يرى فواتيره فقط
        if ($user->account_code === 'cont') {
            return (int) optional($user->contractor)->id === (int) $invoice->contractor_id;
        }

        // غير ذلك: نسمح (للاستخدام الإداري داخل الشركة)
        return true;
    }

    public function update(User $user, ContractorInvoice $invoice): bool
    {
        if ($user->company_code !== $invoice->company_code) {
            return false;
        }

        // المقاول لا يعدّل/يُصدر/يلغي
        if ($user->account_code === 'cont') {
            return false;
        }

        return true;
    }

    public function delete(User $user, ContractorInvoice $invoice): bool
    {
        return $this->update($user, $invoice);
    }
}

