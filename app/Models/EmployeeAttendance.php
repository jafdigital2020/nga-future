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

    // public function getTotalHoursAttribute()
    // {
    //     if (!empty($this->timeOut)) {
    //         $startTime = Carbon::parse($this->timeIn);
    //         $endTime = Carbon::parse($this->timeOut);

    //         // Check if the user timed in before 10:00:00 AM
    //         $shiftStart = Carbon::parse('10:00:00');
    //         if ($startTime < $shiftStart) {
    //             $startTime = $shiftStart;
    //         }

    //         // Check if it is already 07:00:00 PM
    //         $now = Carbon::now('Asia/Manila');
    //         $isAfterSevenPM = $now->format('H:i:s') >= '19:00:00';

    //         if ($isAfterSevenPM) {
    //             $endTime = Carbon::parse($this->timeEnd);
    //         }

    //         $breakEndTime = Carbon::parse($this->breakEnd);
    //         $breakOutTime = Carbon::parse($this->breakOut);

    //         // Make sure break time is within the shift time and breakOut is after breakEnd
    //         if ($breakOutTime > $breakEndTime && $breakOutTime <= $endTime) {
    //             // Calculate break duration
    //             $breakDuration = $breakOutTime->diffInSeconds($breakEndTime);

    //             // Deduct break duration from total worked time
    //             $totalWorkedSeconds = $endTime->diffInSeconds($startTime) - $breakDuration;

    //             // Deduct 1 hour as break time from total worked time if it exceeds 1 hour
    //             if ($totalWorkedSeconds > 3600) {
    //                 $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds)
    //             }

    //             // Check if $this->breakLate is a numeric value
    //             if (is_numeric($this->breakLate)) {
    //                 // Deduct breakLate from total worked time
    //                 $totalWorkedSeconds -= $this->breakLate;
    //             }

    //             $hours = floor($totalWorkedSeconds / 3600);
    //             $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //             $seconds = $totalWorkedSeconds % 60;

    //             $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    //         } else {
    //             // No break taken, calculate total worked time without deduction
    //             $totalWorkedSeconds = $endTime->diffInSeconds($startTime);

    //             // Deduct 1 hour as break time from total worked time if it exceeds 1 hour
    //             if ($totalWorkedSeconds > 3600) {
    //                 $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds)
    //             }

    //             // Check if $this->breakLate is a numeric value
    //             if (is_numeric($this->breakLate)) {
    //                 // Deduct breakLate from total worked time
    //                 $totalWorkedSeconds -= $this->breakLate;
    //             }

    //             // Check if the total worked time is over 1 hour
    //             if ($totalWorkedSeconds > 3600) {
    //                 // Deduct 1 hour as break time from total worked time
    //                 $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds)
    //             }

    //             $hours = floor($totalWorkedSeconds / 3600);
    //             $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //             $seconds = $totalWorkedSeconds % 60;

    //             $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    //         }

    //         // Check if break late exceeds a threshold
    //         $breakLateThreshold = 300; // 5 minutes = 300 seconds
    //         if ($this->breakLate > $breakLateThreshold) {
    //             $message = 'You are over the break time by ' . $this->breakLate;
    //             echo "<script>showAlert('$message');</script>";
    //         }

    //         return $timeTotal;
    //     }
    // }


    // protected static function booted()
    // {
    //     static::saving(function ($employeeattendance) {
    //         if ($employeeattendance->isDirty('timeOut')) {
    //             // Only calculate and update timeTotal if timeOut is set
    //             if (!empty($employeeattendance->timeEnd)) {
    //                 // Calculate total break late
    //                 $breakEndTime = Carbon::parse($employeeattendance->breakEnd);
    //                 $breakOutTime = Carbon::parse($employeeattendance->breakOut);

    //                 // Check if break is late (breakOut is after breakEnd)
    //                 if ($breakOutTime > $breakEndTime && $breakOutTime > $employeeattendance->timeEnd) {
    //                     $totalBreakLateSeconds = $breakOutTime->diffInSeconds($breakEndTime); // Calculate break late duration in seconds

    //                     // Convert totalBreakLateSeconds to HH:MM:SS format
    //                     $totalBreakLate = gmdate("H:i:s", $totalBreakLateSeconds);

    //                     // Save total break late to the model
    //                     $employeeattendance->breakLate = $totalBreakLate;

    //                     // Deduct break late from timeTotal
    //                     $employeeattendance->timeTotal -= $totalBreakLateSeconds;

    //                     // Check if break late exceeds a threshold (e.g., 5 minutes = 300 seconds)
    //                     $breakLateThreshold = 300;
    //                     if ($totalBreakLateSeconds > $breakLateThreshold) {
    //                         // Trigger a JavaScript alert with the break late message
    //                         $message = 'You are over the break time by ' . $totalBreakLate;
    //                         echo "<script>showAlert('$message');</script>";
    //                     }
    //                 } else {
    //                     $totalBreakLate = '00:00:00'; // Set break late to 00:00:00 if the break is not late

    //                     // Save total break late to the model
    //                     $employeeattendance->breakLate = $totalBreakLate;
    //                 }

    //                 // Recalculate and update total worked time by calling getTotalHoursAttribute()
    //                 $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
    //             }
    //         }
    //     });
    // }

    public function getTotalHoursAttribute()
    {
        if (!empty($this->timeOut)) {
            $startTime = Carbon::parse($this->timeIn);
            $endTime = Carbon::parse($this->timeOut);

            // Check if the user timed in before 10:00:00 AM
            $shiftStart = Carbon::parse('10:00:00');
            if ($startTime < $shiftStart) {
                $startTime = $shiftStart;
            }

            // Calculate the total worked seconds
            $totalWorkedSeconds = $endTime->diffInSeconds($startTime);

            // Check if breakOut and breakEnd exist and calculate break duration
            if (!empty($this->breakOut) && !empty($this->breakEnd)) {
                $breakOutTime = Carbon::parse($this->breakOut);
                $breakEndTime = Carbon::parse($this->breakEnd);

                if ($breakOutTime > $breakEndTime && $breakOutTime <= $endTime) {
                    $breakDuration = $breakOutTime->diffInSeconds($breakEndTime);
                    $totalWorkedSeconds -= $breakDuration;
                }

                // Deduct 1 hour for the break if the total worked time exceeds 1 hour
                if ($totalWorkedSeconds > 3600) {
                    $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds)
                }
            }

            // Check if $this->breakLate is a numeric value
            if (is_numeric($this->breakLate)) {
                // Deduct breakLate from total worked time
                $totalWorkedSeconds -= $this->breakLate;
            }

            $hours = floor($totalWorkedSeconds / 3600);
            $minutes = floor(($totalWorkedSeconds % 3600) / 60);
            $seconds = $totalWorkedSeconds % 60;

            return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        }

        return '00:00:00';
    }

    protected static function booted()
    {
        static::saving(function ($employeeattendance) {
            if ($employeeattendance->isDirty('timeOut')) {
                if (!empty($employeeattendance->timeEnd)) {
                    // Calculate total break late
                    $breakEndTime = Carbon::parse($employeeattendance->breakEnd);
                    $breakOutTime = Carbon::parse($employeeattendance->breakOut);

                    if ($breakOutTime > $breakEndTime) {
                        $totalBreakLateSeconds = $breakOutTime->diffInSeconds($breakEndTime);
                        $totalBreakLate = gmdate("H:i:s", $totalBreakLateSeconds);
                        $employeeattendance->breakLate = $totalBreakLate;

                        $breakLateThreshold = 300;
                        if ($totalBreakLateSeconds > $breakLateThreshold) {
                            $message = 'You are over the break time by ' . $totalBreakLate;
                            echo "<script>showAlert('$message');</script>";
                        }
                    } else {
                        $employeeattendance->breakLate = '00:00:00';
                    }

                    $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
                }
            }
        });
    }


}
