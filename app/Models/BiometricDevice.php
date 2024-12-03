<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BiometricDevice extends Model
{
    use HasFactory;

    protected $fillable = ['device_name', 'ip_address', 'port', 'location'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_device')
                    ->withPivot('biometric_user_id')
                    ->withTimestamps();
    }
}
