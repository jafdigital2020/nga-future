<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Salary;
use Carbon\CarbonInterval;
use App\Models\EmployeeSalary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAttendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = [
        'users_id',
        'name',
        'date',
        'timeIn',
        'breakIn',
        'breakEnd',
        'breakOut',
        'timeEnd',
        'timeOut',
        'status',
        'totalLate',
        'breakLate',
    ];

    public function employeeSalary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function getTotalHoursAttribute()
    {
        if (!empty($this->timeOut)) {
            $startTime = Carbon::parse($this->timeIn);
            $endTime = Carbon::parse($this->timeOut);
    
            // Calculate the total worked time in seconds
            $totalWorkedSeconds = $endTime->diffInSeconds($startTime);
    
            // Calculate the break duration in seconds if both breakIn and breakOut are provided
            if (!empty($this->breakIn) && !empty($this->breakOut)) {
                $breakInTime = Carbon::parse($this->breakIn);
                $breakOutTime = Carbon::parse($this->breakOut);
                $breakDuration = $breakOutTime->diffInSeconds($breakInTime);
            } else {
                $breakDuration = 0;
            }
    
            // Deduct break duration from total worked time
            $totalWorkedSeconds -= $breakDuration;
    
            // Check if $this->breakLate is a numeric value
            if (is_numeric($this->breakLate)) {
                // Deduct breakLate from total worked time
                $totalWorkedSeconds -= $this->breakLate;
            }
    
            // Limit the total worked hours to the max allowed hours (8 hours = 28800 seconds)
            $maxWorkedHours = 28800; // 8 hours in seconds
            if ($totalWorkedSeconds > $maxWorkedHours) {
                $totalWorkedSeconds = $maxWorkedHours; // Limit the total to the max allowed hours
            }
    
            // Calculate hours, minutes, and seconds
            $hours = floor($totalWorkedSeconds / 3600);
            $minutes = floor(($totalWorkedSeconds % 3600) / 60);
            $seconds = $totalWorkedSeconds % 60;
    
            // Format the total time as HH:MM:SS
            $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    
            return $timeTotal;
        }
    }
    
    protected static function booted()
    {
        static::saving(function ($employeeattendance) {
            if ($employeeattendance->isDirty('timeOut')) {
                // Only calculate and update timeTotal if timeOut is set
                if (!empty($employeeattendance->timeEnd)) {
                    // Calculate total break late
                    $breakEndTime = Carbon::parse($employeeattendance->breakEnd);
                    $breakOutTime = Carbon::parse($employeeattendance->breakOut);

                    // Check if break is late (breakOut is after breakEnd)
                    if ($breakOutTime > $breakEndTime && $breakOutTime > $employeeattendance->timeEnd) {
                        $totalBreakLateSeconds = $breakOutTime->diffInSeconds($breakEndTime); // Calculate break late duration in seconds

                        // Convert totalBreakLateSeconds to HH:MM:SS format
                        $totalBreakLate = gmdate("H:i:s", $totalBreakLateSeconds);

                        // Save total break late to the model
                        $employeeattendance->breakLate = $totalBreakLate;

                        // Deduct break late from timeTotal
                        $employeeattendance->timeTotal -= $totalBreakLateSeconds;

                        // Check if break late exceeds a threshold (e.g., 5 minutes = 300 seconds)
                        $breakLateThreshold = 300;
                        if ($totalBreakLateSeconds > $breakLateThreshold) {
                            // Trigger a JavaScript alert with the break late message
                            $message = 'You are over the break time by ' . $totalBreakLate;
                            echo "<script>showAlert('$message');</script>";
                        }
                    } else {
                        $totalBreakLate = '00:00:00'; // Set break late to 00:00:00 if the break is not late

                        // Save total break late to the model
                        $employeeattendance->breakLate = $totalBreakLate;
                    }

                    // Recalculate and update total worked time by calling getTotalHoursAttribute()
                    $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
                }
            }
        });
    }
    
}
