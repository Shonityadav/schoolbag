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
        Schema::create('id_card_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('category_id')->nullable(); // For future gallery categories
            $table->string('name');
            $table->enum('type', ['student', 'staff']);
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->longText('front_layout_json')->nullable();
            $table->longText('back_layout_json')->nullable();
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_card_templates');
    }
};
