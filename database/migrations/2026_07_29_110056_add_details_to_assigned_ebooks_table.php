<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('assigned_ebooks', function (Blueprint $table) {
            $table->string('standard')->nullable()->after('title');
            $table->string('subject')->nullable()->after('standard');
            $table->string('publication')->nullable()->after('subject');
            $table->string('series')->nullable()->after('publication');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assigned_ebooks', function (Blueprint $table) {
            $table->dropColumn(['standard', 'subject', 'publication', 'series']);
        });
    }
};
