<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_display_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 40)->index();
            $table->string('block_type', 40)->index();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('list_items')->nullable();
            $table->string('icon_fa', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('public_display_videos', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 40)->index();
            $table->string('youtube_url', 512);
            $table->string('title')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('public_contact_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_type', 30)->index();
            $table->string('label')->nullable();
            $table->string('value', 512);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_contact_channels');
        Schema::dropIfExists('public_display_videos');
        Schema::dropIfExists('public_display_blocks');
    }
};
