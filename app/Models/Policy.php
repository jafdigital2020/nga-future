<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;
    protected $fillable = [
        'policyTitle',
        'policyName',
        'policyDescription',
        'policyUpload',
        'uploaded_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
