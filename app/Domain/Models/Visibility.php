<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    use HasFactory;
    protected $fillable = ['visible_id', 'visible_type', 'status'];

    public function visible()
    {
        return $this->morphTo();
    }

    public function scopePublic($query)
    {
        return $query->where('status', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('status', 'private');
    }

    public static function updateVisibilityStatus($visible, $status)
    {
        return self::updateOrCreate(
            ['visible_id' => $visible->id, 'visible_type' => get_class($visible)],
            ['status' => $status]
        );
    }
}
