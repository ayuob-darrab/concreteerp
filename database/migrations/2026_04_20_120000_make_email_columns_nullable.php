<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * السماح بقيمة NULL لعمود email حيث كان NOT NULL (MySQL).
     * باقي الجداول التي تحتوي email كانت nullable مسبقاً.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        if (Schema::hasTable('companies') && $this->columnExists('companies', 'email')) {
            DB::statement('ALTER TABLE `companies` MODIFY `email` VARCHAR(100) NULL');
        }

        if (Schema::hasTable('users') && $this->columnExists('users', 'email')) {
            DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
        }

        if (Schema::hasTable('password_resets') && $this->columnExists('password_resets', 'email')) {
            DB::statement('ALTER TABLE `password_resets` MODIFY `email` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        if (Schema::hasTable('password_resets') && $this->columnExists('password_resets', 'email')) {
            DB::statement('ALTER TABLE `password_resets` MODIFY `email` VARCHAR(255) NOT NULL');
        }

        if (Schema::hasTable('users') && $this->columnExists('users', 'email')) {
            DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL');
        }

        if (Schema::hasTable('companies') && $this->columnExists('companies', 'email')) {
            DB::statement('ALTER TABLE `companies` MODIFY `email` VARCHAR(100) NOT NULL');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
};
