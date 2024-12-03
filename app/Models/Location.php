<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_name',
        'location_address',
        'created_by',
        'edit_by',
    ];

    public function userLocation()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function locationCreatedBy()
    {
       return $this->belongsTo(User::class, 'created_by');
    }

    public function locationEditBy()
    {
       return $this->belongsTo(User::class, 'edit_by');
    }
}
