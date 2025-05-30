<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Shared\Traits\savingUserIdModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    use HasFactory, savingUserIdModelTrait;

    protected $fillable = ['description', 'location', 'user_id'];


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

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }

    public function approval()
    {
        return $this->morphOne(PostApproval::class, 'approvable');
    }

    public function visibility()
    {
        return $this->morphOne(Visibility::class, 'visible');
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

    public function getGoogleMapsLinkAttribute()
    {
        if (empty($this->location)) {
            return null;
        }
        $locationParts = explode(',', $this->location);

        if (count($locationParts) < 2) {
            return null;
        }
        [$latitude, $longitude] = $locationParts;

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return null;
        }

        return "https://www.google.com/maps?q={$latitude},{$longitude}";
    }

    public function isOwnedBy($user = null)
    {
        $user = $user ?? auth()->user();
        return $this->user_id === $user->id;
    }
}
