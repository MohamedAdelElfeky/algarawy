<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';
    protected $fillable = [
        // 'name',
        'description',
        'location',
        'discount',
        'user_id',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(CourseFile::class);
    }

    public function images_and_videos()
    {
        return $this->hasMany(CourseImageVideo::class);
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
