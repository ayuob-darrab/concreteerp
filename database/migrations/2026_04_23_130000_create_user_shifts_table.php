<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_shifts')) {
            Schema::create('user_shifts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('shift_id');
                $table->timestamps();

                $table->unique(['user_id', 'shift_id'], 'user_shifts_user_shift_unique');
                $table->index('user_id');
                $table->index('shift_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shifts');
    }
};

