<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name', 'start_date', 'end_date', 'reason', 'type', 'status', 'approved_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
