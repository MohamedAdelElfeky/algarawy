<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'points_required', 'benefits'];

    public function assignments()
    {
        return $this->hasMany(MembershipAssignment::class);
    }
}
