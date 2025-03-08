<?php

namespace App\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Meeting extends Model
{
    protected $fillable = [
        'user_id',
        'datetime',
        'link',
        'name',
        'start_time',
        'end_time',
        'description',
        'type',
    ];
    
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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

        return $query->whereHas('approval', function ($query) use ($status) {
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

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }
}
