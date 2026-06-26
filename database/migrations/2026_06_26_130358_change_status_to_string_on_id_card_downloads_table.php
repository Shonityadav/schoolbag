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
        Schema::table('id_card_downloads', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id_card_downloads', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Processing', 'Completed', 'Failed'])->default('Pending')->change();
        });
    }
};
