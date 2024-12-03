<?php

namespace App\Models;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLocation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'location_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locationSetting()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
