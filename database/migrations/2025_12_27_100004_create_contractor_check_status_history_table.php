<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contractor_check_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->constrained('contractor_checks')->onDelete('cascade');
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_check_status_history');
    }
};
