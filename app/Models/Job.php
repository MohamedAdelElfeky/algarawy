<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $table = 'jobs';
    protected $fillable = [
        'name',
        'description',
        'qualifications',
        'location',
        'contact_information',
        'user_id',
        'photo',
        'company_type',
        'company_logo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
