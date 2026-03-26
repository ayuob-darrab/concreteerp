<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->comment('عنوان يظهر في ترويسة الصفحة');
            $table->text('intro_text')->nullable()->comment('النص التمهيدي');
            $table->text('hint_text')->nullable()->comment('نص تلميح أسفل وسائل التواصل');
            $table->string('email', 255)->nullable();
            $table->string('whatsapp', 100)->nullable();
            $table->string('telegram', 255)->nullable();
            $table->string('facebook', 512)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->string('phone', 80)->nullable();
            $table->timestamps();
        });

        if (DB::table('public_contact_settings')->exists()) {
            return;
        }

        $now = now();
        $row = [
            'title' => null,
            'intro_text' => null,
            'hint_text' => null,
            'email' => null,
            'whatsapp' => null,
            'telegram' => null,
            'facebook' => null,
            'instagram' => null,
            'phone' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (Schema::hasTable('public_display_blocks')) {
            $row['intro_text'] = DB::table('public_display_blocks')
                ->where('page_key', 'contact')->where('block_type', 'welcome')->value('body') ?? $row['intro_text'];
            $row['hint_text'] = DB::table('public_display_blocks')
                ->where('page_key', 'contact')->where('block_type', 'hint')->value('body') ?? $row['hint_text'];
        }

        if (Schema::hasTable('public_contact_channels')) {
            $channels = DB::table('public_contact_channels')->where('is_active', 1)->orderBy('sort_order')->get();
            foreach ($channels as $ch) {
                $t = $ch->channel_type;
                if (in_array($t, ['email', 'whatsapp', 'telegram', 'instagram', 'phone'], true) && $row[$t] === null) {
                    $row[$t] = $ch->value;
                }
            }
        }

        DB::table('public_contact_settings')->insert($row);
    }

    public function down(): void
    {
        Schema::dropIfExists('public_contact_settings');
    }
};
