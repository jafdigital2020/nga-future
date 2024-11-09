<?php

namespace App\Models;

use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = ['users_id', 'leave_type_id', 'name', 'start_date', 'end_date', 'reason', 'type', 'status', 'approved_by', 'attached_file'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
