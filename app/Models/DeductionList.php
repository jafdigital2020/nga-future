<?php

namespace App\Models;

use App\Models\UserDeduction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeductionList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'amount', 'type', 'inclusion_limit', 'is_every_payroll'];

    public function userDeductions()
    {
        return $this->hasMany(UserDeduction::class, 'deduction_id', 'id');
    }
}
