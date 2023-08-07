<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function pdfs(): MorphMany
    {
        return $this->morphMany(FilePdf::class, 'pdfable');
    }
}
