<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';
    protected $fillable = [
        'name',
        'description',
        'files',
        'location',
        'discount',
        'user_id',
        'link',
        'images_and_videos',
    ];

    protected $casts = [
        'files' => 'array',
        'images_and_videos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
