<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserGeofence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeofencingSetting extends Model
{
    use HasFactory;

    protected $fillable = [

        'fencing_name',
        'fencing_address',
        'latitude',
        'longitude',
        'fencing_radius',
        'created_by',
        'edit_by',
     ];

     public function users()
     {
         return $this->belongsToMany(User::class, 'user_geofences', 'geofence_id', 'user_id');
     }
 
     // Define relationship to access pivot table records directly
     public function userGeofences()
     {
         return $this->hasMany(UserGeofence::class);
     }

     public function geofenceCreatedBy()
     {
        return $this->belongsTo(User::class, 'created_by');
     }

     public function geofenceEditBy()
     {
        return $this->belongsTo(User::class, 'edit_by');
     }
}
