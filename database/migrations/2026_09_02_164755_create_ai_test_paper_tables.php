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
        Schema::create('ai_ques_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('marks')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_paper_generators', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('set_id')->index(); // the auto incrementing integer per set
            $table->unsignedBigInteger('ebook_id')->index();
            $table->string('chapter')->nullable();
            $table->string('chapter_num')->nullable();
            $table->string('section')->nullable();
            $table->unsignedBigInteger('ques_type_id')->nullable();
            $table->longText('question')->nullable();
            $table->longText('diagrams')->nullable();
            $table->longText('answer')->nullable();
            $table->longText('answer_text')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_paper_generators');
        Schema::dropIfExists('ai_ques_types');
    }
};
