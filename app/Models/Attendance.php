<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * OLD + NEW fields together
     * (nothing removed)
     */
    protected $fillable = [
        // OLD (kept)
        'companyName',
        'remark',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

        // NEW (added for new system)
        'institute_id',
        'created_for',
        'attendance_date',
    ];

    /* ===========================
       NEW RELATIONSHIPS (ADDED)
       =========================== */

    // Attendance belongs to a user (staff / student)
    public function user()
    {
        return $this->belongsTo(User::class, 'created_for');
    }

    // Attendance belongs to a school
    public function intitute()
    {
        return $this->belongsTo(Institute::class, 'school_id');
    }

    // Who marked the attendance
    public function marker()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ===========================
       OLD LOGIC (UNCHANGED)
       =========================== */

    public function attendancesByUser($id)
    {
        $attendances = DB::table('attendances')
            ->selectRaw('
                DATE(created_at) as attendance_date,
                MAX(CASE WHEN status = "Check-in" THEN "✔" END) as Present,
                MAX(CASE WHEN status = "Check-out" THEN "✘" END) as Absent,
                MAX(CASE WHEN status = "Check-in" THEN TIME(created_at) END) as present_time,
                MAX(CASE WHEN status = "Check-out" THEN TIME(created_at) END) as absent_time,
                MAX(CASE 
                    WHEN status = "Check-in" THEN 8
                    WHEN status IN ("Check-in", "Check-out") THEN 0
                    ELSE 0
                END) as working_hours
            ')
            ->where('created_by', $id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('attendance_date', 'asc')
            ->get();

        return $attendances;
    }
}
