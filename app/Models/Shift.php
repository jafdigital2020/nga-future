<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name', 'start_time', 'late_threshold', 'end_time', 'break_time',
        'recurring', 'repeat_every', 'days', 'end_on', 'indefinite', 'tag', 'note'
    ];

    protected $casts = [
        'days' => 'array',  
        'recurring' => 'boolean',
        'indefinite' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
}
