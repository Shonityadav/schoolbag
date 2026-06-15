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
        Schema::rename('courses', 'assigned_ebooks');
        Schema::rename('students', 'student_details');
        Schema::rename('staffs', 'staff_details');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('assigned_ebooks', 'courses');
        Schema::rename('student_details', 'students');
        Schema::rename('staff_details', 'staffs');
    }
};
