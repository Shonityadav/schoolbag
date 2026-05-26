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
        Schema::create('ebook_chapter_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ebook_id');
            $table->unsignedBigInteger('ebook_chapter_id');
            $table->integer('stage_number');
            $table->string('stage_name');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('ebook_chapter_id')->references('id')->on('ebook_chapters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ebook_chapter_stages');
    }
};
