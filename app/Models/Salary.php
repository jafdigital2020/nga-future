<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $table = 'salaries';
    protected $fillable = [
        'users_id',
        'fullName',
        'position',
        'year',
        'month',
        'transactionDate',
        'start_date',
        'end_date',
        'totalHours',
        'sss',
        'philHealth',
        'pagIbig',
        'withHolding',
        'late',
        'loan',
        'advance',
        'others',
        'birthdayPTO',
        'vacLeave',
        'sickLeave',
        'otTotal',
        'bonus',
        'thirteenthMonth',
        'totalDeduction',
        'totalEarning',
        'grossMonthly',
        'grossBasic',
        'netPayTotal',
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
