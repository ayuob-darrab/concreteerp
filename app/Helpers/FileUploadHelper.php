<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * الامتدادات المسموح بها للصور
     */
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    /**
     * الامتدادات المسموح بها للمستندات
     */
    public const DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

    /**
     * الامتدادات المسموح بها للنسخ الاحتياطية
     */
    public const BACKUP_EXTENSIONS = ['zip', 'sql', 'gz', 'tar'];

    /**
     * جميع الامتدادات المسموح بها
     */
    public const ALL_ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'zip',
        'sql',
        'gz',
        'tar'
    ];

    /**
     * توليد اسم ملف آمن وفريد
     *
     * @param UploadedFile $file
     * @param array|null $allowedExtensions الامتدادات المسموح بها (null = الكل)
     * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
     */
    public static function generateSecureFilename(UploadedFile $file, ?array $allowedExtensions = null): array
    {
        // الحصول على الامتداد الأصلي
        $extension = strtolower($file->getClientOriginalExtension());

        // التحقق من الامتداد
        $allowed = $allowedExtensions ?? self::ALL_ALLOWED_EXTENSIONS;
        if (!in_array($extension, $allowed)) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'نوع الملف غير مسموح به. الأنواع المسموحة: ' . implode(', ', $allowed)
            ];
        }

        // التحقق من MIME type للصور
        if (in_array($extension, self::IMAGE_EXTENSIONS)) {
            $mimeType = $file->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            if (!in_array($mimeType, $allowedMimes)) {
                return [
                    'success' => false,
                    'filename' => null,
                    'error' => 'نوع الملف لا يتطابق مع الامتداد'
                ];
            }
        }

        // توليد اسم فريد وآمن
        $uniqueName = time() . '_' . Str::random(16) . '.' . $extension;

        return [
            'success' => true,
            'filename' => $uniqueName,
            'error' => null
        ];
    }

    /**
     * رفع ملف بشكل آمن
     *
     * @param UploadedFile $file
     * @param string $uploadPath المسار الكامل للمجلد
     * @param array|null $allowedExtensions
     * @return array ['success' => bool, 'path' => string|null, 'filename' => string|null, 'error' => string|null]
     */
    public static function uploadSecurely(UploadedFile $file, string $uploadPath, ?array $allowedExtensions = null): array
    {
        $result = self::generateSecureFilename($file, $allowedExtensions);

        if (!$result['success']) {
            return [
                'success' => false,
                'path' => null,
                'filename' => null,
                'error' => $result['error']
            ];
        }

        // إنشاء المجلد بصلاحيات آمنة
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // نقل الملف
        $file->move($uploadPath, $result['filename']);

        return [
            'success' => true,
            'path' => $uploadPath . '/' . $result['filename'],
            'filename' => $result['filename'],
            'error' => null
        ];
    }

    /**
     * التحقق من صحة اسم ملف النسخة الاحتياطية (منع Directory Traversal)
     *
     * @param string $filename
     * @return array ['valid' => bool, 'sanitized' => string|null, 'error' => string|null]
     */
    public static function validateBackupFilename(string $filename): array
    {
        // إزالة أي مسارات - الحصول على اسم الملف فقط
        $sanitized = basename($filename);

        // التحقق من أن الاسم لا يحتوي على أحرف خطيرة
        if ($sanitized !== $filename || preg_match('/[<>:"\/\\|?*\x00-\x1f]/', $sanitized)) {
            return [
                'valid' => false,
                'sanitized' => null,
                'error' => 'اسم الملف غير صالح'
            ];
        }

        // التحقق من الامتداد
        $extension = strtolower(pathinfo($sanitized, PATHINFO_EXTENSION));
        if (!in_array($extension, self::BACKUP_EXTENSIONS)) {
            return [
                'valid' => false,
                'sanitized' => null,
                'error' => 'نوع الملف غير مسموح به للنسخ الاحتياطية'
            ];
        }

        // التحقق من أن الاسم يتطابق مع نمط النسخ الاحتياطية
        if (!preg_match('/^[\w\-\.]+\.(zip|sql|gz|tar)$/i', $sanitized)) {
            return [
                'valid' => false,
                'sanitized' => null,
                'error' => 'تنسيق اسم الملف غير صالح'
            ];
        }

        return [
            'valid' => true,
            'sanitized' => $sanitized,
            'error' => null
        ];
    }
}
