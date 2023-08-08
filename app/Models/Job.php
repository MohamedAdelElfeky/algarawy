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
}
