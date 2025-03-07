<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
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

    // public function ApprovalStatus($status)
    // {
    //     $allowedStatuses = ['pending', 'approved', 'rejected'];
    //     // dd($status);
    //     if (!in_array($status, $allowedStatuses)) {
    //         $status = 'pending';
    //     }

    //     return $this->postApproval()->updateOrCreate(
    //         ['approvable_id' => $this->id, 'approvable_type' => self::class],
    //         ['status' => $status]
    //     );
    // }

    // public function VisibilityStatus($status)
    // {
    //     $allowedStatuses = ['public', 'private'];

    //     if (!in_array($status, $allowedStatuses)) {
    //         $status = 'pending';
    //     }

    //     return $this->postVisibility()->updateOrCreate(
    //         ['visible_id' => $this->id, 'visible_type' => self::class],
    //         ['status' => $status]
    //     );
    // }
}
