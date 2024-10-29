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

    public function getTotalHoursAttribute()
    {
        // Check if timeOut or timeEnd exists
        if (!empty($this->timeOut) || !empty($this->timeEnd)) {
            $timeIn = Carbon::parse($this->timeIn);
            $breakIn = !empty($this->breakIn) ? Carbon::parse($this->breakIn) : null;
            $breakOut = !empty($this->breakOut) ? Carbon::parse($this->breakOut) : null;
            $timeOut = Carbon::parse($this->timeOut ?? Carbon::now('Asia/Manila'));
            $timeEnd = Carbon::parse($this->timeEnd ?? Carbon::now('Asia/Manila'));
    
            // Intialize
            $totalWorkedSeconds = 0;
    
            // Get the users shift schedule
            $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();
    
            if ($shiftSchedule) {
                $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
    
                // Flexible 
                if ($shiftSchedule->isFlexibleTime) {
                  
                    if ($breakIn && $breakOut) {
                        // From timeIn to breakIn, and from breakOut to timeOut
                        $totalWorkedSeconds += $timeIn->diffInSeconds($breakOut);
                        $totalWorkedSeconds += $breakIn->diffInSeconds($timeOut);
                    } else {
                        // No breaks, calculate directly from timeIn to timeOut
                        $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
                    }
    
                    // Cap total worked time to allowed hours for flexible shifts
                    if ($totalWorkedSeconds > $allowedHours) {
                        $totalWorkedSeconds = $allowedHours;
                    }
                } else {
                    // Non-flexible shift logic
                    // Case 1: User has breakIn and breakOut
                    if ($breakIn && $breakOut) {
                        // If timeOut is greater than timeEnd, calculate using timeEnd
                        if ($timeOut->greaterThan($timeEnd)) {
                            // From timeIn to breakIn
                            $totalWorkedSeconds += $timeIn->diffInSeconds($breakOut);
                            // From breakOut to timeEnd
                            $totalWorkedSeconds += $breakIn->diffInSeconds($timeEnd);
                        } else {
                            // From timeIn to breakIn
                            $totalWorkedSeconds += $timeIn->diffInSeconds($breakOut);
                            // From breakOut to timeOut
                            $totalWorkedSeconds += $breakIn->diffInSeconds($timeOut);
                        }
                    }
                    // Case 2: No breakIn and breakOut
                    else {
                        // If timeOut is greater than timeEnd, calculate using timeEnd
                        if ($timeOut->greaterThan($timeEnd)) {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($timeEnd);
                        } else {
                            $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
                        }
                    }
    
                    // Cap total worked time to allowed hours for non-flexible shifts
                    if ($totalWorkedSeconds > $allowedHours) {
                        $totalWorkedSeconds = $allowedHours;
                    }
                }
            } else {
                // Handle case where shift schedule is not found
                $allowedHours = 0; // or another default value
            }
    
            // Ensure the total worked seconds are not negative
            if ($totalWorkedSeconds < 0) {
                $totalWorkedSeconds = 0;
            }
    
            // Calculate hours, minutes, and seconds
            $hours = floor($totalWorkedSeconds / 3600);
            $minutes = floor(($totalWorkedSeconds % 3600) / 60);
            $seconds = $totalWorkedSeconds % 60;
    
            // Format the total time as HH:MM:SS
            return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        }
    
        return '00:00:00';
    }

    // public function getTotalHoursAttribute()
    // {
    //     // Check if timeOut or timeEnd exists
    //     if (!empty($this->timeOut) || !empty($this->timeEnd)) {
    //         $timeIn = Carbon::parse($this->timeIn);
    //         $breakIn = !empty($this->breakIn) ? Carbon::parse($this->breakIn) : null;
    //         $breakOut = !empty($this->breakOut) ? Carbon::parse($this->breakOut) : null;
    //         $breakEnd = !empty($this->breakEnd) ? Carbon::parse($this->breakEnd) : null; // Add breakEnd
    //         $timeOut = Carbon::parse($this->timeOut ?? Carbon::now('Asia/Manila'));
    //         $timeEnd = Carbon::parse($this->timeEnd ?? Carbon::now('Asia/Manila'));

    //         // Initialize
    //         $totalWorkedSeconds = 0;

    //         // Get the user's shift schedule
    //         $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();

    //         if ($shiftSchedule) {
    //             $shiftStart = Carbon::parse($shiftSchedule->shiftStart);
    //             $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd);
    //             $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();

    //             // Flexible shift logic
    //             if ($shiftSchedule->isFlexibleTime) {
    //                 if ($breakIn && $breakOut) {
    //                     // Compute timeIn to breakIn
    //                     $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);

    //                     // Handle breakEnd and breakOut
    //                     if ($breakOut->lt($breakEnd)) {
    //                         // If breakOut is earlier than breakEnd, compute from breakEnd
    //                         $totalWorkedSeconds += $breakEnd->diffInSeconds($timeOut);
    //                     } else {
    //                         // If breakOut is later than breakEnd, compute from breakOut
    //                         $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
    //                     }
    //                 } else {
    //                     // No break, compute directly from timeIn to timeOut
    //                     $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
    //                 }

    //                 // Cap total worked time to allowed hours for flexible shifts
    //                 if ($totalWorkedSeconds > $allowedHours) {
    //                     $totalWorkedSeconds = $allowedHours;
    //                 }
    //             } 
    //             // Non-flexible shift logic
    //             else {
    //                 // Case 1: User has breakIn and breakOut
    //                 if ($breakIn && $breakOut) {
    //                     if ($timeIn->lt($shiftStart)) {
    //                         // ShiftStart + BreakIn logic
    //                         $totalWorkedSeconds += $shiftStart->diffInSeconds($breakIn);
    //                     } else {
    //                         // TimeIn + BreakIn logic
    //                         $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                     }

    //                     // Handle breakOut vs. breakEnd
    //                     if ($breakOut->lt($breakEnd)) {
    //                         // If breakOut is earlier than breakEnd
    //                         if ($timeOut->lt($shiftEnd)) {
    //                             $totalWorkedSeconds += $breakEnd->diffInSeconds($timeOut);
    //                         } else {
    //                             $totalWorkedSeconds += $breakEnd->diffInSeconds($shiftEnd);
    //                         }
    //                     } else {
    //                         // If breakOut is later than breakEnd
    //                         if ($timeOut->lt($shiftEnd)) {
    //                             $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
    //                         } else {
    //                             $totalWorkedSeconds += $breakOut->diffInSeconds($shiftEnd);
    //                         }
    //                     }
    //                 } 
    //                 // Case 2: No breakIn and breakOut
    //                 else {
    //                     if ($timeIn->lt($shiftStart)) {
    //                         // TimeIn earlier than shiftStart
    //                         if ($timeOut->gt($shiftEnd)) {
    //                             $totalWorkedSeconds += $shiftStart->diffInSeconds($shiftEnd);
    //                         } else {
    //                             $totalWorkedSeconds += $shiftStart->diffInSeconds($timeOut);
    //                         }
    //                     } else {
    //                         // TimeIn after shiftStart
    //                         if ($timeOut->gt($shiftEnd)) {
    //                             $totalWorkedSeconds += $timeIn->diffInSeconds($shiftEnd);
    //                         } else {
    //                             $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
    //                         }
    //                     }
    //                 }

    //                 // Cap total worked time to allowed hours for non-flexible shifts
    //                 if ($totalWorkedSeconds > $allowedHours) {
    //                     $totalWorkedSeconds = $allowedHours;
    //                 }
    //             }
    //         } else {
    //             // Handle case where shift schedule is not found
    //             $allowedHours = 0; // or another default value
    //         }

    //         // Ensure the total worked seconds are not negative
    //         if ($totalWorkedSeconds < 0) {
    //             $totalWorkedSeconds = 0;
    //         }

    //         // Calculate hours, minutes, and seconds
    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;

    //         // Format the total time as HH:MM:SS AM/PM
    //         return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    //     }

    //     return '00:00:00';
    // }

    
    // public function getTotalHoursAttribute()
    // {
    //     if (!empty($this->timeOut) || !empty($this->timeEnd)) {
    //         $timeIn = Carbon::parse($this->timeIn);
    //         $breakIn = !empty($this->breakIn) ? Carbon::parse($this->breakIn) : null;
    //         $breakOut = !empty($this->breakOut) ? Carbon::parse($this->breakOut) : null;
    //         $timeOut = Carbon::parse($this->timeOut ?? Carbon::now('Asia/Manila'));
    //         $timeEnd = Carbon::parse($this->timeEnd ?? Carbon::now('Asia/Manila'));

    //         // Initialize total worked seconds
    //         $totalWorkedSeconds = 0;

    //         // Case 1: User has breakIn and breakOut
    //         if ($breakIn && $breakOut) {
    //             // If timeOut is greater than timeEnd, calculate using timeEnd
    //             if ($timeOut->greaterThan($timeEnd)) {
    //                 // From timeIn to breakIn
    //                 $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                 // From breakOut to timeEnd
    //                 $totalWorkedSeconds += $breakOut->diffInSeconds($timeEnd);
    //             } else {
    //                 // From timeIn to breakIn
    //                 $totalWorkedSeconds += $timeIn->diffInSeconds($breakIn);
    //                 // From breakOut to timeOut
    //                 $totalWorkedSeconds += $breakOut->diffInSeconds($timeOut);
    //             }
    //         }
    //         // Case 2: No breakIn and breakOut
    //         else {
    //             // If timeOut is greater than timeEnd, calculate using timeEnd
    //             if ($timeOut->greaterThan($timeEnd)) {
    //                 $totalWorkedSeconds += $timeIn->diffInSeconds($timeEnd);
    //             } else {
    //                 $totalWorkedSeconds += $timeIn->diffInSeconds($timeOut);
    //             }
    //         }

    //         // Ensure the total worked seconds are not negative
    //         if ($totalWorkedSeconds < 0) {
    //             $totalWorkedSeconds = 0;
    //         }

    //         // Cap total worked time to allowed hours
    //         $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();

    //         // Check if $shiftSchedule is not null
    //         if ($shiftSchedule) {
    //             $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
    //             if ($totalWorkedSeconds > $allowedHours) {
    //                 $totalWorkedSeconds = $allowedHours;
    //             }
    //         } else {
    //             // Handle case where shift schedule is not found
    //             // You can set a default allowed hours or log an error
    //             $allowedHours = 0; // or another default value
    //         }

    //         // Calculate hours, minutes, and seconds
    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;

    //         // Format the total time as HH:MM:SS
    //         return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    //     }

    //     return '00:00:00';
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
    
                        // Convert timeTotal from "HH:MM:SS" format to seconds
                        $timeTotalSeconds = strtotime($employeeattendance->timeTotal) - strtotime('TODAY');
    
                        // Deduct break late from timeTotal
                        $timeTotalSeconds -= $totalBreakLateSeconds;
    
                        // Convert back to "HH:MM:SS" format
                        $employeeattendance->timeTotal = gmdate("H:i:s", $timeTotalSeconds);
    
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
    


    // public function getTotalHoursAttribute()
    // {
    //     if (!empty($this->timeOut) || !empty($this->timeEnd)) {
    //         $startTime = Carbon::parse($this->timeIn);
    //         $endTime = Carbon::parse($this->timeOut ?? $this->timeEnd ?? Carbon::now('Asia/Manila'));
    //         $shiftEndTime = Carbon::parse($this->shiftEnd); // Assuming shiftEnd is stored as a string in the format HH:MM:SS
    
    //         // Calculate the total worked time in seconds
    //         $totalWorkedSeconds = $endTime->diffInSeconds($startTime);
    
    //         // Calculate the break duration in seconds if both breakIn and breakOut are provided
    //         $breakDuration = (!empty($this->breakIn) && !empty($this->breakOut))
    //             ? Carbon::parse($this->breakOut)->diffInSeconds(Carbon::parse($this->breakIn))
    //             : 0;
    
    //         // Deduct break duration from total worked time
    //         $totalWorkedSeconds -= $breakDuration;
    
    
    //         // Deduct totalLate if it exists and is in the format 00:00:00
    //         if (!empty($this->totalLate)) {
    //             $totalLateSeconds = Carbon::parse($this->totalLate)->secondsSinceMidnight();
    
    //             // Check if the employee worked beyond shiftEnd and logged in late
    //             if ($endTime->greaterThan($shiftEndTime)) {
    //                 // Calculate time beyond shiftEnd
    //                 $overtimeSeconds = $endTime->diffInSeconds($shiftEndTime);
    
    //                 // If overtime is greater than or equal to totalLate, deduct totalLate from totalWorkedSeconds
    //                 if ($overtimeSeconds >= $totalLateSeconds) {
    //                     $totalWorkedSeconds -= $totalLateSeconds;
    //                 } else {
    //                     // If overtime is less than totalLate, deduct the remaining late time from totalWorkedSeconds
    //                     $totalWorkedSeconds -= ($totalLateSeconds - $overtimeSeconds);
    //                 }
    //             } else {
    //                 // Deduct the entire totalLate if no overtime
    //                 $totalWorkedSeconds -= $totalLateSeconds;
    //             }
    //         }
    
    //         // Ensure the total worked seconds are not negative
    //         if ($totalWorkedSeconds < 0) {
    //             $totalWorkedSeconds = 0;
    //         }
    
    //         // Limit the total worked hours to the max allowed hours (8 hours = 28800 seconds)
    //         $maxWorkedHours = 28800; // 8 hours in seconds
    //         if ($totalWorkedSeconds > $maxWorkedHours) {
    //             $totalWorkedSeconds = $maxWorkedHours;
    //         }
    
    //         // Calculate hours, minutes, and seconds
    //         $hours = floor($totalWorkedSeconds / 3600);
    //         $minutes = floor(($totalWorkedSeconds % 3600) / 60);
    //         $seconds = $totalWorkedSeconds % 60;
    
    //         // Format the total time as HH:MM:SS
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
    
    
}
