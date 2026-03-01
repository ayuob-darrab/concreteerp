<?php

namespace App\Helpers;

use App\Models\Company;

class RandomCodeGenerator
{
    /**
     * توليد كود عشوائي من أحرف وأرقام
     */
    public static function generateCompanycode($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        do {
            $randomCode = '';
            for ($i = 0; $i < $length; $i++) {
                $randomCode .= $characters[rand(0, $charactersLength - 1)];
            }
            // تحقق من عدم التكرار
        } while (Company::where('code', $randomCode)->exists());

        return $randomCode;
    }
}
