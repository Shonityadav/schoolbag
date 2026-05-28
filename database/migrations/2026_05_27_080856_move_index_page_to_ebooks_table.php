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
        Schema::table('ebooks', function (Blueprint $table) {
            $table->string('index_page')->nullable()->after('file_name');
        });

        if (Schema::hasColumn('ebook_chapters', 'index_page')) {
            Schema::table('ebook_chapters', function (Blueprint $table) {
                $table->dropColumn('index_page');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebook_chapters', function (Blueprint $table) {
            $table->string('index_page')->nullable()->after('end_page');
        });

        if (Schema::hasColumn('ebooks', 'index_page')) {
            Schema::table('ebooks', function (Blueprint $table) {
                $table->dropColumn('index_page');
            });
        }
    }
};
