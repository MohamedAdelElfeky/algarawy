<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Job extends Model
{
    use HasFactory;
    protected $table = 'jobs';
    protected $fillable = [
        'description',
        'title',

        'company_name',
        'company_description',
        'company_location',
        'company_type',
        'company_link',
        'company_logo',

        'job_type',
        'job_duration',
        'is_training',
        'price',
        'job_status',

        'user_id',
        'region_id',
        'city_id',
        'neighborhood_id',
        'company_region_id',
        'company_city_id',
        'company_neighborhood_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function pdfs(): MorphMany
    {
        return $this->morphMany(FilePdf::class, 'pdfable');
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    public function companyRegion()
    {
        return $this->belongsTo(Region::class, 'company_region_id');
    }

    public function companyCity()
    {
        return $this->belongsTo(City::class, 'company_city_id');
    }

    public function companyNeighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'company_neighborhood_id');
    }
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }
    public function getCountOfApplicationsAttribute()
    {
        return $this->jobApplications()->count();
    }
    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }
   
}
