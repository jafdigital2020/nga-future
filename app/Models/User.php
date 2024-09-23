<?php

namespace App\Models;

use Log;
use Exception;
use Carbon\Carbon;
use App\Models\Salary;
use App\Models\Payroll;
use Carbon\CarbonInterval;
use App\Models\LeaveRequest;
use App\Models\ShiftSchedule;
use App\Models\EmployeeSalary;
use App\Models\BankInformation;
use App\Models\EmploymentRecord;
use App\Models\EmployementSalary;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 1;
    const ROLE_HR = 2;
    const ROLE_EMPLOYEE = 3;
    const ROLE_OPERATIONS_MANAGER = 4;
    const ROLE_IT_MANAGER = 5;
    const ROLE_MARKETING_MANAGER = 6;

    public $timestamps = true;

    public function getRoleAttribute()
    {
        switch ($this->attributes['role_as']) {
            case self::ROLE_ADMIN:
                return 'Admin';
            case self::ROLE_HR:
                return 'HR';
            case self::ROLE_EMPLOYEE:
                return 'Employee';
            case self::ROLE_OPERATIONS_MANAGER:
                return 'Operations Manager';
            case self::ROLE_IT_MANAGER:
                return 'IT Manager';
            case self::ROLE_MARKETING_MANAGER:
                return 'Marketing Manager';
            default:
                return 'Unknown';
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'fName',
        'mName',
        'lName',
        'email',
        'password',
        'empNumber',
        'typeOfContract',
        'phoneNumber',
        'dateHired',
        'birthday',
        'completeAddress',
        'mSalary',
        'position',
        'role_as',
        'sss',
        'pagIbig',
        'philHealth',
        'tin',
        'department',
        'bdayLeave',
        'vacLeave',
        'sickLeave',
        'reporting_to',
        'image',
    ];

    public function isSupervisor()
    {
        return in_array($this->role_as, [
            self::ROLE_OPERATIONS_MANAGER,
            self::ROLE_IT_MANAGER,
            self::ROLE_MARKETING_MANAGER
        ]);
    }

    public function isAdmin()
    {
        return $this->role_as == self::ROLE_ADMIN;
    }

    public function isHR()
    {
        return $this->role_as == self::ROLE_HR;
    }

    public static function getSupervisorForDepartment($department, $loggedInUser)
    {
        if ($loggedInUser->isSupervisor() || $loggedInUser->isHr()) {
            return 'Management';
        }
        
        $supervisorRoles = [
            'IT' => self::ROLE_IT_MANAGER,
            'Website Development' => self::ROLE_IT_MANAGER,
            'Marketing' => self::ROLE_MARKETING_MANAGER,
            'SEO' => self::ROLE_OPERATIONS_MANAGER,
            'Content' => self::ROLE_OPERATIONS_MANAGER,
        ];

        $supervisorRole = $supervisorRoles[$department] ?? null;

        if ($supervisorRole) {
            return self::where('role_as', $supervisorRole)->first();
        }

        return null;
    }

    public static function getUsersByDepartments(array $departments)
    {
        $supervisorRoles = [
            self::ROLE_OPERATIONS_MANAGER,
            self::ROLE_IT_MANAGER,
            self::ROLE_MARKETING_MANAGER,
        ];

        return self::whereIn('department', $departments)
                   ->whereNotIn('role_as', $supervisorRoles)
                   ->get();
    }
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employeeAttendance ()
    {
        return $this->hasMany(EmployeeAttendance::class, 'users_id' , 'id');
    }

    public function editAttendance()
    {
        return $this->hasMany(EmployeeAttendance::class, 'users_id', 'edited_by');
    }

    public function employeeSalary()
    {
        return $this->hasMany(Salary::class, 'users_id', 'id');
    }

    public function contactEmergency(): HasMany
    {
        return $this->hasMany(ContactEmergency::class, 'users_id', 'id');
    }

    public function personalInformation(): HasMany
    {
        return $this->hasMany(PersonalInformation::class, 'users_id', 'id');
    }

    public function bankInfo(): HasMany
    {
        return $this->hasMany(BankInformation::class, 'users_id', 'id');
    }

    public function employmentRecord (): HasMany
    {
        return $this->hasMany(EmploymentRecord::class, 'users_id', 'id');
    }

    public function employmentSalary (): HasMany
    {
        return $this->hasMany(EmployementSalary::class, 'users_id', 'id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'users_id', 'id');
    }

    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function attendanceRequest()
    {
        return $this->hasMany(ApprovedAttendance::class, 'users_id', 'id');
    }

    public function approvedAttendanceRequest()
    {
        return $this->hasMany(ApprovedAttendance::class, 'approved_by');
    }

    public function payrollPayslip()
    {
        return $this->hasMany(Payroll::class, 'users_id', 'id');
    }

    public function shiftSchedule()
    {
        return $this->hasMany(ShiftSchedule::class, 'users_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'reporting_to');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_to');
    }

    public function checkIn()
{
    try {
        $now = $this->freshTimestamp();

        // Check if the user has already timed in for the day
        $employeeAttendance = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->first();

        if ($employeeAttendance) {
            return back()->with('error', 'You have already timed in!');
        }

        // Get the user's shift schedule
        $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();

        if (!$shiftSchedule) {
            return back()->with('error', 'Shift schedule not found.');
        }

        $timeIn = Carbon::now('Asia/Manila');

        // Check if the current time is past the shift end time
        $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila');
        if ($timeIn->greaterThan($shiftEnd)) {
            return back()->with('error', 'You cannot clock in because your shift has already ended.');
        }

        // Determine status and total late based on shift times if not flexible
        $status = 'On Time';
        $totalLate = '00:00:00';

        // Handling for non-flexible shifts
        if (!$shiftSchedule->isFlexibleTime) {
            $shiftStart = Carbon::parse($shiftSchedule->shiftStart, 'Asia/Manila');
            $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');

            // If the user is late
            if ($timeIn->gt($lateThreshold)) {
                $status = 'Late';
                $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
                $hours = floor($totalLateInSeconds / 3600);
                $minutes = floor(($totalLateInSeconds % 3600) / 60);
                $seconds = $totalLateInSeconds % 60;
                $totalLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            // If timeIn is greater than shiftStart (i.e., user is late), set timeEnd to shiftEnd
            if ($timeIn->greaterThan($shiftStart)) {
                $timeEnd = $shiftEnd;
            } else {
                // Calculate timeEnd based on allowedHours
                $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
                $timeEnd = $timeIn->copy()->addSeconds($allowedHours)->format('h:i:s A');

                // If calculated timeEnd exceeds shiftEnd, cap timeEnd to shiftEnd
                if (Carbon::parse($timeEnd, 'Asia/Manila')->greaterThan($shiftEnd)) {
                    $timeEnd = $shiftEnd;
                }
            }

            // Assign shiftEnd to shiftOver
            $shiftOver = $shiftEnd;
        } else {
            // For flexible time, no need to calculate shiftEnd or timeEnd
            $timeEnd = null;  // Flexible schedules can have dynamic end times
            $shiftOver = null;  // No fixed shift end for flexible schedules
        }

        // Create the attendance record
        $this->employeeAttendance()->create([
            'name' => auth()->user()->fName . ' ' . auth()->user()->lName,
            'date' => Carbon::now('Asia/Manila')->toDateString(),
            'timeIn' => $timeIn->format('h:i:s A'),
            'status' => $status,
            'totalLate' => $totalLate,
            'timeEnd' => $timeEnd,  // Store calculated timeEnd
            'shiftOver' => $shiftOver,  // Store shiftEnd as shiftOver
        ]);

        return back()->with('success', 'Time in successfully! You are ' . $status . '. Welcome.');

    } catch (Exception $e) {
        // Log the error for debugging purposes
        Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);

        // Return a generic error message to the user
        return back()->with('error', 'An unexpected error occurred. Please try again later.');
    }
}


    // public function checkIn()
    // {
    //     try {
    //         $now = $this->freshTimestamp();

    //         // Check if the user has already timed in for the day
    //         $employeeAttendance = $this->employeeAttendance()
    //             ->where('date', Carbon::now('Asia/Manila')->toDateString())
    //             ->first();

    //         if ($employeeAttendance) {
    //             return back()->with('error', 'You have already timed in!');
    //         }

    //         // Get the user's shift schedule
    //         $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();

    //         if (!$shiftSchedule) {
    //             return back()->with('error', 'Shift schedule not found.');
    //         }

    //         $timeIn = Carbon::now('Asia/Manila');

    //         // Determine status and total late based on shift times if not flexible
    //         $status = 'On Time';
    //         $totalLate = '00:00:00';

    //         // Handling for non-flexible shifts
    //         if (!$shiftSchedule->isFlexibleTime) {
    //             $shiftStart = Carbon::parse($shiftSchedule->shiftStart, 'Asia/Manila');
    //             $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');
    //             $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila')->format('h:i:s A');

    //             // If the user is late
    //             if ($timeIn->gt($lateThreshold)) {
    //                 $status = 'Late';
    //                 $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
    //                 $hours = floor($totalLateInSeconds / 3600);
    //                 $minutes = floor(($totalLateInSeconds % 3600) / 60);
    //                 $seconds = $totalLateInSeconds % 60;
    //                 $totalLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    //             }

    //             // If timeIn is greater than shiftStart (i.e., user is late), set timeEnd to shiftEnd
    //             if ($timeIn->greaterThan($shiftStart)) {
    //                 $timeEnd = $shiftEnd;
    //             } else {
    //                 // Calculate timeEnd based on allowedHours
    //                 $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
    //                 $timeEnd = $timeIn->copy()->addSeconds($allowedHours)->format('h:i:s A');

    //                 // If calculated timeEnd exceeds shiftEnd, cap timeEnd to shiftEnd
    //                 if (Carbon::parse($timeEnd, 'Asia/Manila')->greaterThan($shiftEnd)) {
    //                     $timeEnd = $shiftEnd;
    //                 }
    //             }

    //             // Assign shiftEnd to shiftOver
    //             $shiftOver = $shiftEnd;
    //         } else {
    //             // For flexible time, no need to calculate shiftEnd or timeEnd
    //             $timeEnd = null;  // Flexible schedules can have dynamic end times
    //             $shiftOver = null;  // No fixed shift end for flexible schedules
    //         }

    //         // Create the attendance record
    //         $this->employeeAttendance()->create([
    //             'name' => auth()->user()->fName . ' ' . auth()->user()->lName,
    //             'date' => Carbon::now('Asia/Manila')->toDateString(),
    //             'timeIn' => $timeIn->format('h:i:s A'),
    //             'status' => $status,
    //             'totalLate' => $totalLate,
    //             'timeEnd' => $timeEnd,  // Store calculated timeEnd
    //             'shiftOver' => $shiftOver,  // Store shiftEnd as shiftOver
    //         ]);

    //         return back()->with('success', 'Time in successfully! You are ' . $status . '. Welcome.');

    //     } catch (Exception $e) {
    //         // Log the error for debugging purposes
    //         Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);

    //         // Return a generic error message to the user
    //         return back()->with('error', 'An unexpected error occurred. Please try again later.');
    //     }
    // }


    // public function checkIn()
    // {
    //     try {
    //         $now = $this->freshTimestamp();
    
    //         // Check if the user has already timed in for the day
    //         $employeeAttendance = $this->employeeAttendance()
    //                                     ->where('date', Carbon::now('Asia/Manila')->toDateString())
    //                                     ->first();
    
    //         if ($employeeAttendance) {
    //             return back()->with('error', 'You have already timed in!');
    //         }
    
    //         // Get the user's shift schedule
    //         $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();
    
    //         if (!$shiftSchedule) {
    //             return back()->with('error', 'Shift schedule not found.');
    //         }
    
    //         $timeIn = Carbon::now('Asia/Manila');
    
    //         // Determine status and total late based on shift times if not flexible
    //         $status = 'On Time';
    //         $totalLate = '00:00:00';
    
    //         if (!$shiftSchedule->isFlexibleTime) {
    //             $shiftStart = Carbon::parse($shiftSchedule->shiftStart, 'Asia/Manila');
    //             $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');
    
    //             if ($timeIn->gt($lateThreshold)) {
    //                 $status = 'Late';
    //                 $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
    //                 $hours = floor($totalLateInSeconds / 3600);
    //                 $minutes = floor(($totalLateInSeconds % 3600) / 60);
    //                 $seconds = $totalLateInSeconds % 60;
    //                 $totalLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    //             }
    //         }
    
    //         // Create the attendance record
    //         $this->employeeAttendance()->create([
    //             'name' => auth()->user()->fName . ' ' . auth()->user()->lName,
    //             'date' => Carbon::now('Asia/Manila')->toDateString(),
    //             'timeIn' => $timeIn->format('h:i:s A'),
    //             'status' => $status,
    //             'totalLate' => $totalLate
    //         ]);
    
    //         return back()->with('success', 'Time in successfully! You are ' . $status . '. Welcome.');
    
    //     } catch (Exception $e) {
    //         // Log the error for debugging purposes
    //         Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);
    
    //         // Return a generic error message to the user
    //         return back()->with('error', 'An unexpected error occurred. Please try again later.');
    //     }
    // }
    
    public function breakIn()
    {
        $now = $this->freshTimestamp();
        $breakin = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNull('breakIn')
            ->first();
    
        if (!$breakin) {
            return back()->with('error', 'You have already taken a break or have not timed in yet.');
        }
    
        // Check if the user has already timed out
        $timeout = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNotNull('timeOut')
            ->first();
    
        if ($timeout) {
            return back()->with('error', 'You have already timed out.');
        }
    
        $breakInTime = Carbon::now('Asia/Manila');
        $breakEndTime = $breakInTime->copy()->addHour(); // Add 1 hour to the break in time to calculate break end time
    
        $breakin->update([
            'breakIn' => $breakInTime->format('h:i:s A'),
            'breakEnd' => $breakEndTime->format('h:i:s A'),
        ]);
    
        return back()->with('success', 'You have successfully started your break.');
    }
    
    public function breakOut()
    {
        $now = $this->freshTimestamp();
    
        // Check if the user has already timed out
        $timeout = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNotNull('timeOut')
            ->first();
    
        if ($timeout) {
            return back()->with('error', 'You have already timed out.');
        }
    
        $breakout = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNotNull('breakIn')
            ->whereNull('breakOut')
            ->first();
    
        if (!$breakout) {
            return back()->with('error', 'You have not break in yet or you have already broke out.');
        }
    
        $breakInTime = Carbon::parse($breakout->breakInTime);
        $breakOutTime = Carbon::now('Asia/Manila');
    
        $diffInMinutes = $breakInTime->diffInMinutes($breakOutTime);
    
        if ($diffInMinutes > 60) {
            $exceededMinutes = $diffInMinutes - 60;
            $breakLateFormat = CarbonInterval::minutes($exceededMinutes)->cascade()->format('%H:%I:%S');
            $breakout->update([
                'breakLate' => $breakLateFormat,
            ]);
            return back()->with('error', 'You are late for your break out by ' . $breakLateFormat . ' (HH:MM:SS).');
        }
    
        $breakEndTime = Carbon::parse($breakout->breakEnd);
        if ($breakOutTime->greaterThan($breakEndTime)) {
            $exceededMinutes = $breakOutTime->diffInMinutes($breakEndTime);
            $breakLateFormat = CarbonInterval::minutes($exceededMinutes)->cascade()->format('%H:%I:%S');
            $breakout->update([
                'breakLate' => $breakLateFormat,
            ]);
            return back()->with('error', 'You have exceeded your break end time by ' . $breakLateFormat . ' (HH:MM:SS).');
        }
    
        $breakout->update([
            'breakOut' => Carbon::now('Asia/Manila')->format('h:i:s A'),
        ]);
    
        return back()->with('success', 'Welcome Back!');
    }

    public function checkOut()
    {
        try {
            $now = $this->freshTimestamp();
    
            // Check if the user has already timed out for the day
            $timeout = $this->employeeAttendance()
                ->where('date', Carbon::now('Asia/Manila')->toDateString())
                ->whereNotNull('timeOut')
                ->first();
    
            if ($timeout) {
                return back()->with('error', 'You have already timed out.');
            }
    
            // Check if the user has timed in for the day
            $timeIn = $this->employeeAttendance()
                ->where('date', Carbon::now('Asia/Manila')->toDateString())
                ->whereNull('timeOut')
                ->first();
    
            if (!$timeIn) {
                return back()->with('error', "You don't have a time-in record. Please time in first.");
            }
    
            // Check if the user is on a break and hasn't ended it
            $breakOut = $this->employeeAttendance()
                ->where('date', Carbon::now('Asia/Manila')->toDateString())
                ->whereNotNull('breakIn')
                ->whereNull('breakOut')
                ->first();
    
            if ($breakOut) {
                return back()->with('error', 'Please end your break first. Thank you!');
            }
    
            // Record the timeOut
            $timeOut = Carbon::now('Asia/Manila')->format('h:i:s A');
    
            // Update the attendance record with timeOut
            $timeIn->update([
                'timeOut' => $timeOut,
            ]);
    
            return back()->with('success', 'You have successfully timed out. Thank you for your hard work!');
    
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Check-out Error: ' . $e->getMessage(), ['exception' => $e]);
    
            // Return a generic error message to the user
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
    

    
    // public function checkOut()
    // {
    //     $now = $this->freshTimestamp();
    
    //     $timeout = $this->employeeAttendance()
    //         ->where('date', Carbon::now('Asia/Manila')->toDateString())
    //         ->whereNotNull('timeOut')
    //         ->first();
    
    //     if ($timeout) {
    //         return back()->with('error', 'You have already timed out.');
    //     }
    
    //     $timeIn = $this->employeeAttendance()
    //         ->where('date', Carbon::now('Asia/Manila')->toDateString())
    //         ->whereNull('timeOut')
    //         ->first();
    
    //     if (!$timeIn) {
    //         return back()->with('error', "You don't have a time-in record. Please time in first.");
    //     }
    
    //     $breakOut = $this->employeeAttendance()
    //         ->where('date', Carbon::now('Asia/Manila')->toDateString())
    //         ->whereNotNull('breakIn')
    //         ->whereNull('breakOut')
    //         ->first();
    
    //     if ($breakOut) {
    //         return back()->with('error', 'Please End your break first. Thank you!');
    //     }
    
    //     $timeOut = Carbon::now('Asia/Manila')->format('h:i:s A');
    //     $timeInParsed = Carbon::parse($timeIn->timeIn);
    
    //     $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)->first();
    
    //     // Check if the shift schedule exists and if it is flexible time
    //     if ($shiftSchedule && $shiftSchedule->isFlexibleTime) {
    //         // If it's flexible time, we can set timeEnd as the current time or leave it null
    //         $timeEnd = $timeOut ?? Carbon::now('Asia/Manila')->format('h:i:s A');
    //     } else {
    //         // Non-flexible time handling
    //         // Parse allowedHours from the shift schedule and convert it to seconds
    //         $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();

    //         // Add allowedHours to timeInParsed
    //         $timeEnd = $timeInParsed->copy()->addSeconds($allowedHours)->format('h:i:s A');

    //         // Parse shiftEnd and format it
    //         $shiftOver = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila')->format('h:i:s A');
    //     }
    
    //     $timeIn->update([
    //         'timeOut' => $timeOut,
    //         'timeEnd' => $timeEnd,
    //         'shiftOver' => $shiftOver,
    //     ]);
    
    //     return back()->with('success', 'You have successfully timed out. Thank you for your hard work!');
    // }
    
    public function checkForMissedCheckOuts()
    {
        // Get yesterday's date
        $yesterday = Carbon::yesterday()->toDateString();

        // Get all users who checked in yesterday but didn't check out
        $missedLogouts = EmployeeAttendance::where('date', $yesterday)
            ->whereNull('timeOut')  // Users who haven't checked out
            ->get();

        foreach ($missedLogouts as $attendance) {
            $user = User::find($attendance->users_id);

            // Send notifications to HR, supervisor, and admin
            $this->notifyStakeholders($user);
        }
    }

    public function notifyStakeholders($user, $date)
    {
        // Get the supervisor for the user's department
        $supervisor = $user->supervisor;

        // Get all HR users
        $hrUsers = User::where('role_as', User::ROLE_HR)->get();

        // Get all Admin users
        $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();

        // Notify the supervisor
        if ($supervisor && $supervisor != 'Management') {
            $supervisor->notify(new MissedLogoutNotification($user, $date));
        }

        // Notify all HR users
        foreach ($hrUsers as $hr) {
            $hr->notify(new MissedLogoutNotification($user, $date));
        }

        // Notify all Admin users
        foreach ($adminUsers as $admin) {
            $admin->notify(new MissedLogoutNotification($user, $date));
        }
    }

}