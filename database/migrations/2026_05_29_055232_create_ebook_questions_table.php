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
    public function up()
    {
        Schema::create('ebook_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ebook_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedBigInteger('ques_type_id')->nullable();
            $table->longText('question')->nullable();
            $table->string('answer')->nullable();
            $table->string('subject')->nullable();
            $table->timestamps();

            $table->foreign('ebook_id')->references('id')->on('ebooks')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('ebook_chapters')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('ebook_chapter_stages')->onDelete('cascade');
            $table->foreign('ques_type_id')->references('id')->on('question_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ebook_questions');
    }
};
