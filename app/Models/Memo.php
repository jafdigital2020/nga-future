<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'date_issue',
        'attached_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
