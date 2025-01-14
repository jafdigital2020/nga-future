<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceCredit extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'attendanceCredits',
     ];

     public function user()
     {
         return $this->belongsTo(User::class, 'user_id', 'id');
     }
}
