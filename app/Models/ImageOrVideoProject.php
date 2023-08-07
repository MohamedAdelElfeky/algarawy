<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageOrVideoProject extends Model
{
    use HasFactory;
    protected $fillable = ['url'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
