<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    use HasFactory;
    protected $fillable = ['status'];

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
}
