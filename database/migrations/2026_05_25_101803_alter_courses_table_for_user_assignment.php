<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to make class_id nullable without doctrine/dbal
        DB::statement('ALTER TABLE courses MODIFY class_id bigint unsigned NULL');
        
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('ebook_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'ebook_id']);
        });
        
        DB::statement('ALTER TABLE courses MODIFY class_id bigint unsigned NOT NULL');
    }
};
