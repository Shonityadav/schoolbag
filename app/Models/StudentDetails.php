<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentDetails extends Model
{
    use HasFactory;

    protected $table = 'student_details';

    protected $fillable = [
        'created_for',      // FK → users.id (student login)
        'institute_id',     // FK → schools.id
        'class_id',         // FK → classes.id
        'roll_no',
        'fee',
        'fee_period',
        'admission_date',
        'academic_year',
    ];

    /* =========================
       RELATIONSHIPS
    ========================== */

    /**
     * Student login account
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_for');
    }

    /**
     * Student belongs to institute/school
     */
    public function institute()
    {
        return $this->belongsTo(School::class, 'institute_id');
    }

    /**
     * Student belongs to a class
     * NOTE: Do NOT name model "Class" (PHP reserved keyword)
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}

