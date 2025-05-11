<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'personal_title',
        'email',
        'phone',
        'password',
        'national_id',
        'occupation_category',
        'is_whatsapp',
        'birth_date',
        'region_id',
        'city_id',
        'neighborhood_id',
        'is_verified',
    ];

    // protected $hidden = ['password'];
}
