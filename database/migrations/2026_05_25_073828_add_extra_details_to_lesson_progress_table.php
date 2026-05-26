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
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('ebook_id')->nullable()->after('lesson_id');
            $table->string('publication_name')->nullable()->after('ebook_id');
            $table->string('subject')->nullable()->after('publication_name');
            $table->string('standard')->nullable()->after('subject');
            $table->integer('stage_number')->nullable()->after('standard');
            $table->integer('stage_attempt_number')->nullable()->after('stage_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn([
                'ebook_id',
                'publication_name',
                'subject',
                'standard',
                'stage_number',
                'stage_attempt_number'
            ]);
        });
    }
};
