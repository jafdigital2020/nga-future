<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeCredits extends Model
{
    use HasFactory;

    protected $fillable = [

        'users_id',
        'otCredits',
     ];

     public function user()
     {
         return $this->belongsTo(User::class, 'users_id', 'id');
     }
 
}
