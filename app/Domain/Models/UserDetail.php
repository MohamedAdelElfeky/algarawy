<?php

namespace App\Domain\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'location',
        'birthdate',
        'region_id',
        'city_id',
        'neighborhood_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
