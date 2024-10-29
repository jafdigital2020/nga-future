<?php

namespace App\Models;

use App\Models\User;
use App\Models\EarningList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserEarning extends Model
{
    use HasFactory;

    protected $fillable = ['users_id', 'earning_id', 'inclusion_count', 'active'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function earningList()
    {
        return $this->belongsTo(EarningList::class, 'earning_id', 'id');
    }
}
