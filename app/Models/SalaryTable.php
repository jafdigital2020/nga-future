<?php

namespace App\Models;

use App\Models\User;
use App\Models\ApprovedAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'month',
        'cut_off',
        'monthly_salary',
        'daily_rate',
        'hourly_rate',
        'gross_pay',
        'earnings',
        'total_earnings',
        'deductions',
        'total_deductions',
        'loans',
        'total_loans',
        'net_pay',
        'overtimeHours',
        'paidLeave',
        'status',
        'start_date',
        'end_date',
        'year',
        'total_hours',
        'approved_attendance_id',
        'notes',
    ];

    protected $casts = [
        'earnings' => 'array', // Cast earnings as an array
        'deductions' => 'array', // Cast deductions as an array
        'loans' => 'array', // Cast loans as an array
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

   public function approvedAttendance()
   {
        return $this->belongsTo(ApprovedAttendance::class, 'approved_attendance_id');
   }

}
