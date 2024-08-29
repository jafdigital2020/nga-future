<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftSchedule extends Model
{
    use HasFactory;

    protected $fillable = [

       'users_id',
       'shiftStart',
       'lateThreshold',
       'shiftEnd',
       'isFlexibleTime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

}
