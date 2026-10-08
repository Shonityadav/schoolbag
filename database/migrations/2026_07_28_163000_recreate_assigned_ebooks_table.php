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
        // Disable FK checks to drop the existing table and clean historical data
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('assigned_ebooks');

        Schema::create('assigned_ebooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ebook_id', 150)->nullable(); // String to accommodate both integers and short code IDs like 'svp1' at the end of myebook URLs
            $table->text('ebook_url')->nullable();      // Storing URL when QR contains 'myebook'
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('assigned_ebooks');
        Schema::enableForeignKeyConstraints();
    }
};
