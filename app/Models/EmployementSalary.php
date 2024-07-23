<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployementSalary extends Model
{
    use HasFactory;
    protected $table = 'employement_salaries';
    protected $fillable = [
        'users_id',
        'annSalary',
        'salFreqMonthly',
        'salRate',
        'currency',
        'proposalReason',
        'proBy',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
