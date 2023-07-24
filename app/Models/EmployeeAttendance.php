<?php

namespace App\Models;

use Carbon\Carbon;
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
        return $this->belongsTo(EmployeeSalary::class);
    }


// public function getTotalHoursAttribute()
//     {
//         $startTime = Carbon::parse($this->timeIn);
//         $endTime = Carbon::parse($this->timeOut);
//         $totalBreakTime = CarbonInterval::minutes($this->breakOut)->subtract(CarbonInterval::minutes($this->breakIn))->totalMinutes;
//         $totalWorkedMinutes = $endTime->diffInMinutes($startTime) - $totalBreakTime;
//         return round($totalWorkedMinutes / 60, 2); // return the total worked hours rounded to two decimal places
//     }

// protected static function booted()
//     {
//         static::saving(function ($employeeattendance) {
//             $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
//         });
//     }


    // public function getTotalHoursAttribute()
    //     {
    //         $startTime = Carbon::parse($this->timeIn);
    //         $endTime = Carbon::parse($this->timeOut);
    //         $totalBreakTime = CarbonInterval::minutes($this->breakOut)->subtract(CarbonInterval::minutes($this->breakIn))->totalMinutes;
    //         $totalWorkedSeconds = $endTime->diffInSeconds($startTime) - ($totalBreakTime * 60);
    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;
    //         return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); // return the total worked hours in hours:minutes:seconds format
    //     }


    // public function getTotalHoursAttribute()
    // {
    //     $startTime = Carbon::parse($this->timeIn);
    //     $endTime = Carbon::parse($this->timeOut);
    //     $totalWorkedSeconds = $endTime->diffInSeconds($startTime);

    //     if ($totalWorkedSeconds >= 5 * 3600) {
    //         $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds) if worked for 5 or more hours
    //     }

    //     $hours = floor($totalWorkedSeconds / 3600);
    //     $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //     $seconds = $totalWorkedSeconds % 60;
    //     return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); // Return the total worked hours in hours:minutes:seconds format
    // }


    // public function getTotalHoursAttribute()
    // {
    //     $startTime = Carbon::parse($this->timeIn);
    //     $endTime = Carbon::parse($this->timeOut);
    //     $totalWorkedSeconds = $endTime->diffInSeconds($startTime);

    //     $hours = floor($totalWorkedSeconds / 3600);
    //     $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //     $seconds = $totalWorkedSeconds % 60;
    //     return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); // Return the total worked hours in hours:minutes:seconds format
    // }

    // protected static function booted()
    // {
    //     static::saving(function ($employeeattendance) {
    //         if ($employeeattendance->isDirty('timeOut')) {
    //             $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
    //         }
    //     });
    // }


    // public function getTotalHoursAttribute()
    // {
    //     if (!empty($this->timeEnd)) {
    //         $startTime = Carbon::parse($this->timeIn);
    //         $endTime = Carbon::parse($this->timeEnd);
    //         $breakEndTime = Carbon::parse($this->breakEnd);
    //         $breakOutTime = Carbon::parse($this->breakOut);

    //         // Make sure break time is within the shift time and breakOut is after breakEnd
    //         if ($breakOutTime > $breakEndTime && $breakOutTime <= $endTime) {
    //             // Calculate break duration
    //             $breakDuration = $breakOutTime->diffInSeconds($breakEndTime);

    //             // Deduct break duration from total worked time
    //             $totalWorkedSeconds = $endTime->diffInSeconds($startTime) - $breakDuration;
    //         } else {
    //             // No break late, calculate total worked time without deduction
    //             $totalWorkedSeconds = $endTime->diffInSeconds($startTime);
    //         }

    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;

    //         $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    //         // Check if break late exceeds a threshold
    //         $breakLateThreshold = 300; // 5 minutes = 300 seconds
    //         if ($this->breakLate > $breakLateThreshold) {
    //             $message = 'You are over the break time by ' . $this->breakLate;
    //             echo "<script>showAlert('$message');</script>";
    //         }

    //         return $timeTotal;
    //     }
    // }

    // TEST

    // public function getTotalHoursAttribute()
    // {
    //     if (!empty($this->timeEnd)) {
    //         $startTime = Carbon::parse($this->timeIn);
    //         $endTime = Carbon::parse($this->timeOut);

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
    //         } else {
    //             // No break late, calculate total worked time without deduction
    //             $totalWorkedSeconds = $endTime->diffInSeconds($startTime);
    //         }

    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;

    //         $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    //         // Check if break late exceeds a threshold
    //         $breakLateThreshold = 300; // 5 minutes = 300 seconds
    //         if ($this->breakLate > $breakLateThreshold) {
    //             $message = 'You are over the break time by ' . $this->breakLate;
    //             echo "<script>showAlert('$message');</script>";
    //         }

    //         return $timeTotal;
    //     }
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

            // Check if it is already 07:00:00 PM
            $now = Carbon::now('Asia/Manila');
            $isAfterSevenPM = $now->format('H:i:s') >= '19:00:00';

            if ($isAfterSevenPM) {
                $endTime = Carbon::parse($this->timeEnd);
            }

            $breakEndTime = Carbon::parse($this->breakEnd);
            $breakOutTime = Carbon::parse($this->breakOut);

            // Make sure break time is within the shift time and breakOut is after breakEnd
            if ($breakOutTime > $breakEndTime && $breakOutTime <= $endTime) {
                // Calculate break duration
                $breakDuration = $breakOutTime->diffInSeconds($breakEndTime);

                // Deduct break duration from total worked time
                $totalWorkedSeconds = $endTime->diffInSeconds($startTime) - $breakDuration;

                // Check if $this->breakLate is a numeric value
                if (is_numeric($this->breakLate)) {
                    // Deduct breakLate from total worked time
                    $totalWorkedSeconds -= $this->breakLate;
                }

                // Deduct break duration from timeTotal
                $timeTotalSeconds = $totalWorkedSeconds - $breakDuration;
            } else {
                // No break taken, calculate total worked time without deduction
                $totalWorkedSeconds = $endTime->diffInSeconds($startTime);

                // Check if $this->breakLate is a numeric value
                if (is_numeric($this->breakLate)) {
                    // Deduct breakLate from total worked time
                    $totalWorkedSeconds -= $this->breakLate;
                }

                // Check if the total worked time is over 1 hour
                if ($totalWorkedSeconds > 3600) {
                    // Deduct 1 hour as break time from total worked time
                    $totalWorkedSeconds -= 3600; // Deduct 1 hour (3600 seconds)
                }

                // Set timeTotalSeconds to the adjusted total worked time
                $timeTotalSeconds = $totalWorkedSeconds;
            }

            $hours = floor($timeTotalSeconds / 3600);
            $minutes = floor(($timeTotalSeconds % 3600) / 60);
            $seconds = $timeTotalSeconds % 60;

            $timeTotal = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

            // Check if break late exceeds a threshold
            $breakLateThreshold = 300; // 5 minutes = 300 seconds
            if ($this->breakLate > $breakLateThreshold) {
                $message = 'You are over the break time by ' . $this->breakLate;
                echo "<script>showAlert('$message');</script>";
            }

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


    
    // protected static function booted()
    // {
    //     static::saving(function ($employeeattendance) {
    //          $employeeattendance->timeTotal = $employeeattendance->getTotalHoursAttribute();
    //     });
    // }

}
