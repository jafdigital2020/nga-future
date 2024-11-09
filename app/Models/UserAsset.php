<?php

namespace App\Models;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAsset extends Model
{
    use HasFactory;

    protected $fillable = ['users_id', 'asset_id', 'assign_date', 'return_date', 'note'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
}
