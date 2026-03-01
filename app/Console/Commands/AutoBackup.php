<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'تنفيذ النسخ الاحتياطي التلقائي';

    public function handle()
    {
        // التحقق من تفعيل النسخ التلقائي
        $enabled = Setting::where('key', 'auto_backup_enabled')->value('value') === '1';
        if (!$enabled) {
            $this->info('النسخ الاحتياطي التلقائي معطّل');
            return 0;
        }

        // الحصول على الفترة الزمنية
        $interval = (int) Setting::where('key', 'auto_backup_interval')->value('value') ?: 24;
        $lastBackup = Setting::where('key', 'last_auto_backup')->value('value');

        // التحقق من الوقت المناسب للنسخ
        if ($lastBackup) {
            $lastBackupTime = Carbon::parse($lastBackup);
            $nextBackupTime = $lastBackupTime->addHours($interval);

            if (Carbon::now()->lt($nextBackupTime)) {
                $this->info('لم يحن وقت النسخة التالية بعد');
                return 0;
            }
        }

        $this->info('بدء النسخ الاحتياطي التلقائي...');

        try {
            // إنشاء مجلد النسخ التلقائي
            $backupPath = storage_path('app/auto_backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // حذف النسخ القديمة (الاحتفاظ بآخر 5 نسخ فقط)
            $this->cleanOldBackups($backupPath, 5);

            // اسم الملف
            $timestamp = date('Y-m-d_H-i-s');
            $backupFileName = "auto_backup_{$timestamp}";
            $sqlFile = $backupPath . '/' . $backupFileName . '.sql';

            // معلومات الاتصال
            $host = env('DB_HOST', '127.0.0.1');
            $database = env('DB_DATABASE', 'concreteerp');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            // إنشاء نسخة من قاعدة البيانات
            $mysqldumpPath = $this->getMysqldumpPath();

            if ($password) {
                $command = "\"{$mysqldumpPath}\" --host={$host} --user={$username} --password={$password} {$database} > \"{$sqlFile}\"";
            } else {
                $command = "\"{$mysqldumpPath}\" --host={$host} --user={$username} {$database} > \"{$sqlFile}\"";
            }

            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0 || !File::exists($sqlFile) || File::size($sqlFile) == 0) {
                $this->createDatabaseBackupPHP($sqlFile);
            }

            // إنشاء ملف مضغوط
            $zipFileName = "auto_backup_{$timestamp}.zip";
            $zipFilePath = $backupPath . '/' . $zipFileName;

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

                if (File::exists($sqlFile)) {
                    $zip->addFile($sqlFile, "database/{$backupFileName}.sql");
                }

                // إضافة مجلد uploads
                $uploadsPath = public_path('uploads');
                if (File::exists($uploadsPath)) {
                    $this->addFolderToZip($zip, $uploadsPath, 'uploads');
                }

                $zip->close();

                // حساب الحجم والإحصائيات
                $fileSize = $this->formatFileSize(File::size($zipFilePath));
                $companiesCount = Company::where('code', '!=', 'SA')->count();
                $usersCount = User::count();
                $tablesCount = count(DB::select('SHOW TABLES'));

                // حفظ في جدول النسخ
                Backup::create([
                    'name' => $zipFileName,
                    'size' => $fileSize,
                    'companies_count' => $companiesCount,
                    'users_count' => $usersCount,
                    'tables_count' => $tablesCount,
                    'notes' => "نسخة تلقائية (كل {$interval} ساعة)",
                    'created_by' => User::where('usertype_id', 'SA')->first()?->id ?? 1,
                ]);

                // حذف ملف SQL المؤقت
                if (File::exists($sqlFile)) {
                    File::delete($sqlFile);
                }

                // تحديث وقت آخر نسخة
                Setting::updateOrCreate(
                    ['key' => 'last_auto_backup'],
                    ['value' => Carbon::now()->toDateTimeString()]
                );

                $this->info("تم إنشاء النسخة الاحتياطية: {$zipFileName}");
                Log::info("Auto Backup Created: {$zipFileName}");

                return 0;
            }

            $this->error('فشل في إنشاء الملف المضغوط');
            return 1;
        } catch (\Exception $e) {
            $this->error('خطأ: ' . $e->getMessage());
            Log::error('Auto Backup Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * حذف النسخ القديمة
     */
    private function cleanOldBackups($path, $keepCount)
    {
        $files = File::glob($path . '/auto_backup_*.zip');
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $filesToDelete = array_slice($files, $keepCount);
        foreach ($filesToDelete as $file) {
            File::delete($file);
            $this->info("حذف نسخة قديمة: " . basename($file));
        }
    }

    /**
     * الحصول على مسار mysqldump
     */
    private function getMysqldumpPath()
    {
        $paths = [
            'C:/xampp/mysql/bin/mysqldump.exe',
            'C:/wamp/bin/mysql/mysql5.7.26/bin/mysqldump.exe',
            'C:/wamp64/bin/mysql/mysql5.7.26/bin/mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'mysqldump',
        ];

        foreach ($paths as $path) {
            if (file_exists($path) || $path === 'mysqldump') {
                return $path;
            }
        }

        return 'mysqldump';
    }

    /**
     * إنشاء نسخة بطريقة PHP البديلة
     */
    private function createDatabaseBackupPHP($outputFile)
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . env('DB_DATABASE', 'concreteerp');

        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    if ($value === null) return 'NULL';
                    return "'" . addslashes($value) . "'";
                }, (array) $row);

                $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        File::put($outputFile, $sql);
    }

    /**
     * إضافة مجلد إلى الـ ZIP
     */
    private function addFolderToZip($zip, $folder, $zipPath)
    {
        $files = File::allFiles($folder);
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        $directories = File::directories($folder);
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            $this->addFolderToZip($zip, $directory, $zipPath . '/' . $dirName);
        }
    }

    /**
     * تنسيق حجم الملف
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
