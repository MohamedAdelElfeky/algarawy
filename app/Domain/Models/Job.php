<?php

namespace App\Domain\Models;

use App\Models\User;
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
        'job_type',
        'job_duration',
        'is_training',
        'price',
        'job_status',
        'user_id',
        'region_id',
        'city_id',
        'neighborhood_id',
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

    public function JobCompanies()
    {
        return $this->hasOne(JobCompanies::class);
    }

    public function approval()
    {
        return $this->morphOne(PostApproval::class, 'approvable');
    }

    public function scopeApprovalStatus($query, $status = 'pending')
    {
        $allowedStatuses = ['pending', 'approved', 'rejected'];
    
        if (!in_array($status, $allowedStatuses)) {
            $status = 'pending';
        }
    
        return $query->whereHas('postApproval', function ($query) use ($status) {
            $query->where('status', $status);
        });
    }

    public function visibility()
    {
        return $this->morphOne(Visibility::class, 'visible');
    }
    
    public function scopeVisibilityStatus($query, $status = 'public')
    {
        $allowedStatuses = ['public', 'private'];

        if (!in_array($status, $allowedStatuses)) {
            $status = 'public';
        }

        return $query->whereHas('visibility', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function memberships()
    {
        return $this->morphMany(MembershipAssignment::class, 'assignable');
    }
}
