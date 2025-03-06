<?php

namespace  App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostApproval extends Model
{
    use HasFactory;
    protected $fillable = ['approvable_id', 'approvable_type', 'status', 'approved_by', 'notes'];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public static function updateApprovalStatus($approvable, $status, $approvedBy, $notes = null)
    {
        if (!$approvable) {
            throw new \Exception('Approvable model not found.');
        }

        return self::updateOrCreate(
            ['approvable_id' => $approvable->id, 'approvable_type' => get_class($approvable)],
            ['status' => $status, 'approved_by' => $approvedBy, 'notes' => $notes]
        );
    }
}
