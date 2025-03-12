<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'favoritable_id',
        'favoritable_type',
        'user_id'
    ];
    public function favoritable()
    {
        return $this->morphTo();
    }
}
