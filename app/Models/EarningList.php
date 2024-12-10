<?php

namespace App\Models;

use App\Models\UserEarning;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EarningList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'amount', 'type', 'inclusion_limit', 'is_every_payroll', 'tax_type'];

    public function userEarning()
    {
        return $this->hasMany(UserEarning::class, 'earning_id', 'id');
    }
}
