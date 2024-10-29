<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'annTitle',
        'annDescription',
        'posted_by',
        'annImage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}