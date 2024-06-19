<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Salary;
use Carbon\CarbonInterval;
use App\Models\EmployeeSalary;
use App\Models\BankInformation;
use Laravel\Sanctum\HasApiTokens;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
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
        'email',
        'password',
        'image',
        'empNumber',
        'position',
    ];

    public function isSupervisor()
    {
        return in_array($this->role_as, [
            self::ROLE_OPERATIONS_MANAGER,
            self::ROLE_IT_MANAGER,
            self::ROLE_MARKETING_MANAGER
        ]);
    }

    public static function getSupervisorForDepartment($department, $loggedInUser)
    {
        if ($loggedInUser->isSupervisor()) {
            return 'Management';
        }

        switch ($department) {
            case 'IT':
            case 'Website Development':
                $supervisorRole = self::ROLE_IT_MANAGER;
                break;
            case 'Marketing':
                $supervisorRole = self::ROLE_MARKETING_MANAGER;
                break;
            case 'SEO':
            case 'Content':
                $supervisorRole = self::ROLE_OPERATIONS_MANAGER;
                break;
            default:
                return null;
        }

        return self::where('role_as', $supervisorRole)->first();
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

    public function employeeSalary()
    {
        return $this->hasMany(Salary::class, 'users_id', 'id');
    }

    public function contactEmergency(): HasMany
    {
        return $this->hasMany(ContactEmergency::class, 'users_id', 'id');
    }

    public function bankInfo(): HasMany
    {
        return $this->hasMany(BankInformation::class, 'users_id', 'id');
    }

    public function employmentRecord (): HasMany
    {
        return $this->hasMany(EmploymentRecord::class, 'users_id', 'id');
    }


    public function checkIn()
    {
        $now = $this->freshTimestamp();
        $employeeAttendance = $this->employeeAttendance()
                                    ->where('date', Carbon::now('Asia/Manila')->toDateString())
                                    ->first();

        if ($employeeAttendance)
        {
            return back()->with('error', 'You have already Timed in!');
        }
        else
        {
            $shiftStart = Carbon::parse('10:00:00', 'Asia/Manila');
            $shiftEnd = Carbon::parse('19:00:00', 'Asia/Manila');
            $timeIn = Carbon::now('Asia/Manila');

            if ($timeIn->lt($shiftStart)) {
                $status = 'On Time';
                $totalLate = '00:00:00';
            } elseif ($timeIn->gt($shiftEnd)) {
                $status = 'Early Bird';
                $totalLate = '00:00:00';
            } else {
                $status = 'Late';
                $totalLateInSeconds = $timeIn->diffInSeconds($shiftStart);
                $hours = floor($totalLateInSeconds / 3600);
                $minutes = floor(($totalLateInSeconds % 3600) / 60);
                $seconds = $totalLateInSeconds % 60;
                $totalLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            $this->employeeAttendance()->create([
                    'name' => auth()->user()->name,
                    'date' => Carbon::now('Asia/Manila')->toDateString(),
                    'timeIn' => $timeIn->format('h:i:s A'),
                    'status' => $status,
                    'totalLate' => $totalLate // Add total late to the employee attendance record
                ]);

            return back()->with('success', 'Time in Successfully! You are ' . $status . '. Welcome.');
        }
    }

    public function breakIn()
    {
        $now = $this->freshTimestamp();

        $breakin = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNull('breakIn')
            ->first();

        if (!$breakin) {
            return back()->with('error', 'You have already taken a break or have not timed in yet');
        } else {
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
                'breakInTime' => $breakInTime,
                'breakEnd' => $breakEndTime->format('h:i:s A'),
            ]);

            return back()->with('success', 'You have successfully started your break');
        }
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
    
        return back()->with('success', 'You have successfully break out.');
    }

    
    public function checkOut()
    {
        $now = $this->freshTimestamp();

        $timeout = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNotNull('timeOut') // Check for a time-out record first
            ->first();

        if ($timeout) {
            return back()->with('error', "You have already timed out for the day.");
        }

        $timeIn = $this->employeeAttendance()
            ->where('date', Carbon::now('Asia/Manila')->toDateString())
            ->whereNull('timeOut')
            ->first();

        if (!$timeIn) {
            return back()->with('error', "You don't have time in record. Please time in first.");
        }

        $breakOut = $this->employeeAttendance()
             ->where('date', Carbon::now('Asia/Manila')->toDateString())
             ->whereNotNull('breakIn')
             ->whereNull('breakOut')
             ->first();

        if($breakOut) {
            return back()->with('error', "Please Break out first. Thank you!");
        }

        $timeOut = Carbon::now('Asia/Manila')->format('h:i:s A');

        if (Carbon::now('Asia/Manila') >= Carbon::parse('19:00:00')) {
            $timeOut = '07:00:00 PM';
        }

        $timeIn->update([
            'timeOut' => $timeOut,
            'timeEnd' => '07:00:00 PM',
        ]);

        return back()->with('success', 'You have successfully timed out. Thank you for your hard work!');
    }


}