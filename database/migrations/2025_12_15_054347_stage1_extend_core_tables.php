<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Stage1ExtendCoreTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (!Schema::hasColumn('companies', 'code_v2')) {
                    $table->string('code_v2', 10)->nullable()->unique();
                }
                if (!Schema::hasColumn('companies', 'files_path')) {
                    $table->string('files_path', 255)->nullable();
                }
            });
        }

        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                if (!Schema::hasColumn('branches', 'branch_code_v2')) {
                    $table->string('branch_code_v2', 10)->nullable()->unique();
                }
                if (!Schema::hasColumn('branches', 'files_path')) {
                    $table->string('files_path', 255)->nullable();
                }
            });
        }

        if (Schema::hasTable('work_orders')) {
            Schema::table('work_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('work_orders', 'approved_price')) {
                    $table->decimal('approved_price', 15, 2)->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'approved_at')) {
                    $table->dateTime('approved_at')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'approved_note')) {
                    $table->text('approved_note')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'client_approved')) {
                    $table->enum('client_approved', ['accepted', 'rejected', 'edit_requested', 'pending'])->default('pending');
                }
                if (!Schema::hasColumn('work_orders', 'client_approved_at')) {
                    $table->dateTime('client_approved_at')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'execution_date')) {
                    $table->date('execution_date')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'execution_time')) {
                    $table->time('execution_time')->nullable();
                }
                if (!Schema::hasColumn('work_orders', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable();
                }
            });
        }

        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                if (!Schema::hasColumn('materials', 'reserved_quantity')) {
                    $table->decimal('reserved_quantity', 15, 3)->default(0);
                }
                if (!Schema::hasColumn('materials', 'unit_cost')) {
                    $table->decimal('unit_cost', 15, 2)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('materials', 'reserved_quantity')) {
                    $cols[] = 'reserved_quantity';
                }
                if (Schema::hasColumn('materials', 'unit_cost')) {
                    $cols[] = 'unit_cost';
                }
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
}
