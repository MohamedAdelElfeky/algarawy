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
        'photo',
        'company_name', 
        'company_location', 
        'company_type', 
        'company_link', 
        'company_logo', 
        'job_type',
        'is_training', 
        'is_full_time', 
        'price',
        'job_status', 
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
