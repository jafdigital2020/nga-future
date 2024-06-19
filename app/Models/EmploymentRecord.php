<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploymentRecord extends Model
{
    use HasFactory;

    protected $table = 'employment_records';
    protected $fillable = [
        'users_id',
        'hiredDate',
        'supervisor',
        'jobTitle',
        'department',
        'location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
