<?php

namespace App\Models;

use App\Models\User;
use App\Models\DeductionList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDeduction extends Model
{
    use HasFactory;

    protected $fillable = ['users_id', 'deduction_id', 'inclusion_count', 'active'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function deductionList()
    {
        return $this->belongsTo(DeductionList::class, 'deduction_id', 'id');
    }
}
