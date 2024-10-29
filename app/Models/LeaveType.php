<?php

namespace App\Models;

use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['leaveType', 'leaveDays', 'status', 'is_paid'];

    public function leaveCredits()
    {
        return $this->hasMany(LeaveCredit::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
