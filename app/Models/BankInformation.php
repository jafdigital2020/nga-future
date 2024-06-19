<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'bankName',
        'bankAccName',
        'bankAccNumber',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
