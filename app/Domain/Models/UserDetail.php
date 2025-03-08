<?php

namespace App\Domain\Models;

use App\Models\User;
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

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    public function getImageByType(string $type): ?string
    {
        $image = $this->images->where('type', $type)->first();
        return $image ? asset($image->url) : asset('default.png');
    }

    public function getFullLocation(): string
    {
        return trim(implode(' ', array_filter([
            optional($this->region)->name,
            optional($this->city)->name,
            optional($this->neighborhood)->name
        ])));
    }
}
