<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'reason',
        'status',
        'approved_by',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    
    public function otapprover()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
