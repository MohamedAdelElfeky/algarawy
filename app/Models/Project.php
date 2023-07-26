<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'images_or_videos',
        'files_pdf',
        'location',
        'user_id',
    ];

    protected $casts = [
        'images_or_videos' => 'array',
        'files_pdf' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
