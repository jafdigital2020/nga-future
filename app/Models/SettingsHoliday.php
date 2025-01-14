<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingsHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'holidayDate',
        'holidayDay',
        'type',
        'recurring',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'holiday_user', 'holiday_id', 'user_id');
    }
}
