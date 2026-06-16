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
        Schema::create('chat_rooms', function (Blueprint $table) {

    $table->id();

    $table->foreignId('institute_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('name');

    $table->enum('type', [
        'class',
        'staff_category'
    ]);

    $table->foreignId('class_id')
        ->nullable()
        ->constrained('classes')
        ->nullOnDelete();

    $table->foreignId('staff_category_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
