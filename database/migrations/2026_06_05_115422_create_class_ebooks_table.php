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
        Schema::create('class_ebooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ebook_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('institute_id');
            $table->timestamps();
            
            // Add foreign keys if applicable. Assuming ebooks uses unsignedInteger
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('institute_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure unique assignment
            $table->unique(['ebook_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_ebooks');
    }
};
