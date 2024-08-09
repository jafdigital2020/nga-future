<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name', '
        start_date', 
        'end_date', 
        'month', 
        'cut_off', 
        'totalHours', 
        'totalLate', 
        'otHours', 
        'vacLeave', 
        'sickLeave', 
        'bdayLeave',
        'unpaidLeave', 
        'status', 
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function approved()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
