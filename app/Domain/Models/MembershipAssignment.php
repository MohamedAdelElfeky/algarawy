<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipAssignment extends Model
{
    use HasFactory;
    protected $fillable = ['membership_id', 'assignable_type', 'assignable_id'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function assignable()
    {
        return $this->morphTo();
    }
}
