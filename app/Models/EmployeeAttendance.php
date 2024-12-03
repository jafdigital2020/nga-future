<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Salary;
use Carbon\CarbonInterval;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\Log;
use App\Models\AttendanceEditHistory;
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
        'shiftOver',
        'timeOut',
        'status',
        'totalLate',
        'breakLate',
        'edited_by',
        'device',
        'latitude',
        'longitude',
        'location',
        'image_path',
        'status_code',
        'reason',
        'approved_by',
        'night_diff_hours',
    ];

    public function employeeSalary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function edited()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function attendance_approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function editHistory()
    {
        return $this->hasMany(AttendanceEditHistory::class, 'attendance_id');
    }


    public $manualTimeTotal = null;


    // Accessor for total_hours
    public function getTotalHoursAttribute()
    {
        // If manualTimeTotal has been set, use it directly
        if ($this->manualTimeTotal !== null) {
            return $this->manualTimeTotal;
        }

        // Use total_hours field if it is set and valid
        if (!empty($this->total_hours) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $this->total_hours)) {
            return $this->total_hours;
        }

        // Ensure timeIn and either timeOut or timeEnd exist
        if (!empty($this->timeIn) && (!empty($this->timeOut) || !empty($this->timeEnd))) {
            $timeIn = Carbon::parse($this->timeIn, 'Asia/Manila');
            $timeOut = !empty($this->timeOut) ? Carbon::parse($this->timeOut, 'Asia/Manila') : Carbon::now('Asia/Manila');

            // Adjust for midnight shift where timeOut is on the next day
            if ($timeOut->lt($timeIn)) {
                $timeOut->addDay();
            }

            // Initialize total worked seconds
            $totalWorkedSeconds = 0;

            // Retrieve shift schedule and calculate total worked seconds
            $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();
            
            if ($shiftSchedule) {
                $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
                $breakIn = !empty($this->breakIn) ? Carbon::parse($this->breakIn, 'Asia/Manila') : null;
                $breakOut = !empty($this->breakOut) ? Carbon::parse($this->breakOut, 'Asia/Manila') : null;

                if ($breakIn && $breakOut && $breakOut->lt($breakIn)) {
                    $breakOut->addDay();
                }

                if ($shiftSchedule->isFlexibleTime) {
                    if ($breakIn && $breakOut) {
                        $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
                        $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
                    } else {
                        $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
                    }
                    if ($totalWorkedSeconds > $allowedHours) {
                        $totalWorkedSeconds = $allowedHours;
                    }
                } else {
                    $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila');
                    if ($shiftEnd->lt($timeIn)) {
                        $shiftEnd->addDay();
                    }
                    if ($breakIn && $breakOut) {
                        if ($timeOut->greaterThan($shiftEnd)) {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
                            $totalWorkedSeconds += $breakOut->diffInSeconds($shiftEnd);
                        } else {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
                            $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
                        }
                    } else {
                        if ($timeOut->greaterThan($shiftEnd)) {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($shiftEnd);
                        } else {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
                        }
                    }
                    if ($totalWorkedSeconds > $allowedHours) {
                        $totalWorkedSeconds = $allowedHours;
                    }
                }
            }

            return gmdate("H:i:s", $totalWorkedSeconds);
        }

        return '00:00:00';
    }

    // Accessor for night_diff_hours
    public function getNightDiffHoursAttribute()
    {
        if (!empty($this->timeIn) && !empty($this->timeOut)) {
            $timeIn = Carbon::parse($this->timeIn, 'Asia/Manila');
            $timeOut = Carbon::parse($this->timeOut, 'Asia/Manila');

            // Adjust for midnight shift where timeOut is on the next day
            if ($timeOut->lt($timeIn)) {
                $timeOut->addDay();
            }

            // Define night shift period
            $nightShiftStartToday = Carbon::parse('22:00:00', 'Asia/Manila');
            $nightShiftEndNextDay = Carbon::parse('06:00:00', 'Asia/Manila')->addDay();

            // Calculate overlap with night shift
            $night_diff_seconds = 0;
            if ($timeIn <= $nightShiftEndNextDay && $timeOut >= $nightShiftStartToday) {
                $nightStart = $timeIn->max($nightShiftStartToday);
                $nightEnd = $timeOut->min($nightShiftEndNextDay);
                $night_diff_seconds = $nightEnd->diffInSeconds($nightStart);
            }

            return gmdate("H:i:s", $night_diff_seconds);
        }

        return '00:00:00';
    }

    // Accessor for regular_hours
    public function getRegularHoursAttribute()
    {
        // Total work time in seconds
        $totalWorkedSeconds = Carbon::parse($this->timeOut)->diffInSeconds(Carbon::parse($this->timeIn));

        // Night diff hours in seconds
        $night_diff_seconds = Carbon::parse($this->getNightDiffHoursAttribute())->diffInSeconds(Carbon::parse("00:00:00"));

        // Calculate regular hours by subtracting night differential hours
        $regularWorkedSeconds = $totalWorkedSeconds - $night_diff_seconds;

        return gmdate("H:i:s", max($regularWorkedSeconds, 0));
    }


    
    // public function getTotalHoursAttribute()
    // {
    //     // If manualTimeTotal has been set, use it directly
    //     if ($this->manualTimeTotal !== null) {
    //         return $this->manualTimeTotal;
    //     }
    
    //     // Use total_hours field if it is set and valid
    //     if (!empty($this->total_hours) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $this->total_hours)) {
    //         return $this->total_hours;
    //     }
    
    //     // Ensure timeIn and either timeOut or timeEnd exist
    //     if (!empty($this->timeIn) && (!empty($this->timeOut) || !empty($this->timeEnd))) {
    //         $timeIn = Carbon::parse($this->timeIn, 'Asia/Manila');
    //         $timeOut = !empty($this->timeOut) ? Carbon::parse($this->timeOut, 'Asia/Manila') : Carbon::now('Asia/Manila');
    
    //         // Adjust for midnight shift where timeOut is on the next day
    //         if ($timeOut->lt($timeIn)) {
    //             $timeOut->addDay();
    //         }
    
    //         // Initialize total worked seconds
    //         $totalWorkedSeconds = 0;
    
    //         // Retrieve shift schedule
    //         $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();
            
    //         if ($shiftSchedule) {
    //             $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
    //             $breakIn = !empty($this->breakIn) ? Carbon::parse($this->breakIn, 'Asia/Manila') : null;
    //             $breakOut = !empty($this->breakOut) ? Carbon::parse($this->breakOut, 'Asia/Manila') : null;
    
    //             // Adjust for midnight shift on breakOut
    //             if ($breakIn && $breakOut && $breakOut->lt($breakIn)) {
    //                 $breakOut->addDay();
    //             }
    
    //             // Flexible Shift Logic
    //             if ($shiftSchedule->isFlexibleTime) {
    //                 if ($breakIn && $breakOut) {
    //                     // Case with break: Calculate from timeIn to breakIn, and breakOut to timeOut
    //                     $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                     $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
    //                 } else {
    //                     // No breaks: Calculate directly from timeIn to timeOut
    //                     $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
    //                 }
    
    //                 // Cap total worked time to allowed hours for flexible shifts
    //                 if ($totalWorkedSeconds > $allowedHours) {
    //                     $totalWorkedSeconds = $allowedHours;
    //                 }
    //             }
    //             // Non-Flexible Shift Logic
    //             else {
    //                 $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila');
                    
    //                 // Adjust shiftEnd for shifts that cross midnight
    //                 if ($shiftEnd->lt($timeIn)) {
    //                     $shiftEnd->addDay();
    //                 }
    
    //                 // Case 1: User has breakIn and breakOut
    //                 if ($breakIn && $breakOut) {
    //                     if ($timeOut->greaterThan($shiftEnd)) {
    //                         // From timeIn to breakIn, then from breakOut to shiftEnd
    //                         $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                         $totalWorkedSeconds += $breakOut->diffInSeconds($shiftEnd);
    //                     } else {
    //                         // From timeIn to breakIn, then from breakOut to timeOut
    //                         $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                         $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
    //                     }
    //                 }
    //                 // Case 2: No breakIn and breakOut
    //                 else {
    //                     if ($timeOut->greaterThan($shiftEnd)) {
    //                         // Calculate from timeIn to shiftEnd
    //                         $totalWorkedSeconds += $timeIn->diffInSeconds($shiftEnd);
    //                     } else {
    //                         // Calculate from timeIn to timeOut
    //                         $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
    //                     }
    //                 }
    
    //                 // Cap total worked time to allowed hours for non-flexible shifts
    //                 if ($totalWorkedSeconds > $allowedHours) {
    //                     $totalWorkedSeconds = $allowedHours;
    //                 }
    //             }
    //         } else {
    //             // Handle case where shift schedule is not found
    //             $allowedHours = 0;
    //         }
    
    //         // Ensure the total worked seconds are non-negative
    //         if ($totalWorkedSeconds < 0) {
    //             $totalWorkedSeconds = 0;
    //         }
    
    //         // Convert total worked seconds to HH:MM:SS format
    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;
    
    //         return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    //     }
    
    //     return '00:00:00';
    // }
    


    // protected static function booted()
    // {
    //     static::saving(function ($employeeattendance) {
    //         if ($employeeattendance->isDirty('timeOut')) {
    //             // Only calculate and update timeTotal if timeOut is set
    //             if (!empty($employeeattendance->timeEnd) || !empty($employeeattendance->timeOut)) {
    //                 // Calculate total break late
    //                 $breakEndTime = Carbon::parse($employeeattendance->breakEnd);
    //                 $breakOutTime = Carbon::parse($employeeattendance->breakOut);
    
    //                 // Check if break is late (breakOut is after breakEnd)
    //                 if ($breakOutTime > $breakEndTime && $breakOutTime > ($employeeattendance->timeEnd ?? $employeeattendance->timeOut)) {
    //                     $totalBreakLateSeconds = $breakOutTime->diffInSeconds($breakEndTime); // Calculate break late duration in seconds
    
    //                     // Convert totalBreakLateSeconds to HH:MM:SS format
    //                     $totalBreakLate = gmdate("H:i:s", $totalBreakLateSeconds);
    
    //                     // Save total break late to the model
    //                     $employeeattendance->breakLate = $totalBreakLate;
    
    //                     // Convert timeTotal from "HH:MM:SS" format to seconds
    //                     $timeTotalSeconds = strtotime($employeeattendance->timeTotal) - strtotime('TODAY');
    
    //                     // Deduct break late from timeTotal
    //                     $timeTotalSeconds -= $totalBreakLateSeconds;
    
    //                     // Convert back to "HH:MM:SS" format
    //                     $employeeattendance->timeTotal = gmdate("H:i:s", $timeTotalSeconds);
    
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
    
    protected static function booted()
{
    static::saving(function ($employeeattendance) {
        if ($employeeattendance->isDirty('timeOut')) {
            // Only calculate and update timeTotal if timeOut is set
            if (!empty($employeeattendance->timeEnd) || !empty($employeeattendance->timeOut)) {
                
                // Calculate total break late
                $breakEndTime = Carbon::parse($employeeattendance->breakEnd);
                $breakOutTime = Carbon::parse($employeeattendance->breakOut);

                // Check if break is late (breakOut is after breakEnd)
                if ($breakOutTime > $breakEndTime && $breakOutTime > ($employeeattendance->timeEnd ?? $employeeattendance->timeOut)) {
                    $totalBreakLateSeconds = $breakOutTime->diffInSeconds($breakEndTime); // Calculate break late duration in seconds

                    // Convert totalBreakLateSeconds to HH:MM:SS format
                    $totalBreakLate = gmdate("H:i:s", $totalBreakLateSeconds);

                    // Save total break late to the model
                    $employeeattendance->breakLate = $totalBreakLate;

                    // Deduct break late from timeTotal
                    $timeTotalSeconds = strtotime($employeeattendance->timeTotal ?? '00:00:00') - strtotime('TODAY');
                    $timeTotalSeconds -= $totalBreakLateSeconds;

                    // Convert back to "HH:MM:SS" format
                    $employeeattendance->timeTotal = gmdate("H:i:s", max($timeTotalSeconds, 0));

                    // Check if break late exceeds a threshold (e.g., 5 minutes = 300 seconds)
                    $breakLateThreshold = 300;
                    if ($totalBreakLateSeconds > $breakLateThreshold) {
                        // Trigger a JavaScript alert with the break late message
                        $message = 'You are over the break time by ' . $totalBreakLate;
                        echo "<script>showAlert('$message');</script>";
                    }
                } else {
                    // Set break late to 00:00:00 if the break is not late
                    $employeeattendance->breakLate = '00:00:00';
                }

                // Calculate and update timeTotal by combining regular_hours and night_diff_hours
                $totalHours = $employeeattendance->total_hours; // Calls getTotalHoursAttribute
                $nightDiffHours = $employeeattendance->night_diff_hours; // Calls getNightDiffHoursAttribute
                $regularHours = $employeeattendance->regular_hours; // Calls getRegularHoursAttribute

                // Set timeTotal as total hours, with regular and night differential hours
                $employeeattendance->timeTotal = $totalHours;
            }
        }
    });
}



    
}
