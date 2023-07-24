<?php

namespace App\Models;

use App\Models\User;
use App\Models\EmployeeAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSalary extends Model
{
    use HasFactory;
    protected $table = 'employee_salaries';
    protected $fillable = [
        'users_id',
        'employee_name',
        'employee_number',
        'position',
        'cutoff_start_date',
        'cutoff_end_date',
        'total_hours',
        'salary',
        'hour_rate',
        'regular_holiday',
        'special_holiday',
        'working_on_restday',
        'working_on_weekend',
        'working_on_nightshift',
        'birthday_pto_leave',
        'late',
        'absence',
        'withholding_tax',
        'sss',
        'pag_ibig',
        'phil_health',
        'overtime',
        '13th_month',
        'christmas_bonus',
        'food_allowance',
        'performance',
        'others',
        'late_deduction',
        'earnings',
        'total_deduct',
        'gross_monthly',
        'gross_basic',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function employeeAttendance()
    {
        return $this->hasOne(EmployeeAttendance::class, 'employee_salary_id');
    }
}
