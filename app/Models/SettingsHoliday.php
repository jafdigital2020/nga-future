<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsHoliday extends Model
{
    use HasFactory;

    protected $table = 'settings_holidays';
    protected $fillable = [
        'title',
        'holidayDate',
        'holidayDay',
        'type',
    ];
}
