<?php

namespace App\Models;

use App\Models\User;
use App\Models\EmployeeAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceEditHistory extends Model
{
    use HasFactory;

    protected $table = 'attendance_edit_history';

    // Allow mass assignment for these fields
    protected $fillable = [
        'attendance_id',
        'changes',
        'edited_by',
    ];

    /**
     * Relationship to the attendance record.
     */
    public function attendance()
    {
        return $this->belongsTo(EmployeeAttendance::class, 'attendance_id');
    }

    /**
     * Relationship to the user who edited.
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
