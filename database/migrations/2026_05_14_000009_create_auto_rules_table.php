<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auto_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('trigger_event', [
                'quiz_pass', 'quiz_fail', 'chapter_complete',
                'streak_days', 'login', 'worksheet_complete'
            ]);
            $table->integer('trigger_value')->default(0); // e.g. 7 for 7-day streak
            $table->enum('action_type', [
                'unlock_chapter', 'assign_worksheet',
                'add_xp', 'award_badge', 'send_notification'
            ]);
            $table->json('action_payload');  // flexible config
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auto_rules');
    }
};
