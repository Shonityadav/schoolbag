<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('id');
            $table->enum('role', ['student', 'admin'])->default('student')->after('class_id');
            $table->string('avatar')->nullable()->after('role');
            $table->integer('total_xp')->default(0)->after('avatar');
            $table->integer('streak_count')->default(0)->after('total_xp');
            $table->date('last_streak_date')->nullable()->after('streak_count');
            $table->string('phone', 20)->nullable()->after('last_streak_date');
            $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id', 'role', 'avatar', 'total_xp', 'streak_count', 'last_streak_date', 'phone']);
        });
    }
};
