<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'ename',
        'position',
        'department',
        'cut_off',
        'year',
        'transactionDate',
        'start_date',
        'end_date',
        'month',
        'totalHours',
        'totalLate',
        'sss',
        'philHealth',
        'pagIbig',
        'withHolding',
        'late',
        'loan',
        'advance',
        'others',
        'bdayLeave',
        'sickLeave',
        'vacLeave',
        'regHoliday',
        'otTotal',
        'nightDiff',
        'bonus',
        'totalDeduction',
        'grossMonthly',
        'grossBasic',
        'dailyRate',
        'hourlyRate',
        'netPay',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

}
