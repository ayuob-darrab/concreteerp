<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Secure Password Validation Rule
 * 
 * قاعدة تحقق من قوة كلمة المرور:
 * - 8 أحرف على الأقل
 * - حرف كبير واحد على الأقل
 * - حرف صغير واحد على الأقل
 * - رقم واحد على الأقل
 * - رمز خاص واحد على الأقل (اختياري)
 */
class SecurePassword implements ValidationRule
{
    protected int $minLength;
    protected bool $requireUppercase;
    protected bool $requireLowercase;
    protected bool $requireNumbers;
    protected bool $requireSymbols;

    public function __construct(
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumbers = true,
        bool $requireSymbols = false
    ) {
        $this->minLength = $minLength;
        $this->requireUppercase = $requireUppercase;
        $this->requireLowercase = $requireLowercase;
        $this->requireNumbers = $requireNumbers;
        $this->requireSymbols = $requireSymbols;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;
        $errors = [];

        if (strlen($password) < $this->minLength) {
            $errors[] = "يجب أن تكون {$this->minLength} أحرف على الأقل";
        }

        if ($this->requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'يجب أن تحتوي على حرف كبير واحد على الأقل';
        }

        if ($this->requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'يجب أن تحتوي على حرف صغير واحد على الأقل';
        }

        if ($this->requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'يجب أن تحتوي على رقم واحد على الأقل';
        }

        if ($this->requireSymbols && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'يجب أن تحتوي على رمز خاص واحد على الأقل';
        }

        if ($this->isCommonPassword($password)) {
            $errors[] = 'كلمة المرور شائعة جداً وسهلة التخمين';
        }

        if (!empty($errors)) {
            $fail('كلمة المرور ضعيفة: ' . implode('، ', $errors));
        }
    }

    /**
     * التحقق من كلمات المرور الشائعة
     */
    protected function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', 'password123', '123456', '12345678', '123456789',
            'qwerty', 'abc123', 'letmein', 'welcome', 'monkey',
            'admin', 'admin123', 'root', 'master', 'login',
            '1234567890', 'password1', 'iloveyou', 'sunshine',
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * إنشاء قاعدة للإنتاج (أكثر صرامة)
     */
    public static function production(): self
    {
        return new self(
            minLength: 10,
            requireUppercase: true,
            requireLowercase: true,
            requireNumbers: true,
            requireSymbols: true
        );
    }

    /**
     * إنشاء قاعدة عادية
     */
    public static function default(): self
    {
        return new self();
    }
}
