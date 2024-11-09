<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'model', 'manufacturer', 'model', 'manufacturer', 'serial_number', 'purchase_date', 'condition', 'status', 'value'];

    public function userAssets()
    {
        return $this->hasMany(UserAsset::class, 'asset_id', 'id');
    }
}
