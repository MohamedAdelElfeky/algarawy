<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'location',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function imagesOrVideos()
    {
        return $this->hasMany(ImageOrVideo::class);
    }

    public function filesPdf()
    {
        return $this->hasMany(FilePdf::class);
    }
}
