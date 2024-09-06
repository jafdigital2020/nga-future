<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsCompany extends Model
{
    use HasFactory;

    protected $table = 'settings_companies';
    protected $fillable = [
        'company',
        'contactPerson',
        'comAddress',
        'country',
        'province',
        'city',
        'postalCode',
        'comEmail',
        'comPhone',
        'comMobile',
        'comFax',
        'comWebsite',
    ];
}
