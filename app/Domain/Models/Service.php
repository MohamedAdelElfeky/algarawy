<?php

namespace App\Domain\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['user_id', 'description', 'location'];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
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
