<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['users_id', 'amount', 'payable_in_cutoff', 'payable_amount_per_cutoff', 'amount_paid', 'status', 'date_completed', 'loan_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

}
