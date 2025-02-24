<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JobCompanies extends Model
{
    use HasFactory;
    protected $table = 'job_companies';
    protected $fillable = [
        'job_id',
        'name',
        'location',
        'description',
        'type',
        'link',
        'region_id',
        'city_id',
        'neighborhood_id',

    ];


    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
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


    public function job()
    {
        return $this->hasMany(Job::class, 'job_id');
    }


}
