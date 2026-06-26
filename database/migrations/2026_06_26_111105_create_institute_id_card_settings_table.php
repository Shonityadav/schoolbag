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
        Schema::create('institute_id_card_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institute_id')->unique();
            $table->string('primary_color')->default('#000000');
            $table->string('secondary_color')->default('#ffffff');
            $table->string('text_color')->default('#333333');
            $table->boolean('show_qr')->default(true);
            $table->boolean('show_barcode')->default(false);
            $table->boolean('show_signature')->default(true);
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_id_card_settings');
    }
};
