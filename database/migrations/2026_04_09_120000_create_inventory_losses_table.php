<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_losses', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 15)->index();
            $table->unsignedBigInteger('branch_id')->index();

            $table->enum('material_type', ['inventory', 'chemical'])->index();
            $table->string('material_code', 50)->nullable()->index(); // inventories.code
            $table->unsignedBigInteger('material_id')->nullable()->index(); // chemicals.id
            $table->string('material_name', 255);
            $table->string('unit', 20)->nullable();

            // quantities (display vs base)
            $table->decimal('quantity_lost', 15, 4);
            $table->decimal('quantity_base', 15, 4);

            // pricing snapshot
            $table->decimal('unit_cost', 15, 6)->default(0); // per base unit
            $table->decimal('unit_price_display', 15, 6)->default(0); // per displayed unit
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('reported_at')->useCurrent()->index();

            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_losses');
    }
};

