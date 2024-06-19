<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactEmergency extends Model
{
    use HasFactory;

    protected $table = 'emergency_contact';
    protected $fillable = [
        'users_id',
        'primaryName',
        'primaryRelation',
        'primaryPhone',
        'secondName',
        'secondRelation',
        'secondPhone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
