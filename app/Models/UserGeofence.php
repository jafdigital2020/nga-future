<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGeofence extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'geofence_id'];

    // Define relationship back to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function geofenceSetting()
    {
        return $this->belongsTo(GeofencingSetting::class, 'geofence_id');
    }
}
