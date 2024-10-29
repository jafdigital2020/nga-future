<?php

namespace App\Models;

use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveCredit extends Model
{
    use HasFactory;

    protected $table = 'leave_credits';
    protected $fillable = ['user_id', 'leave_type_id', 'remaining_credits'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

}
