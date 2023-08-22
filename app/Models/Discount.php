<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $fillable = [
        'description',
        'location',
        'discount',
        'price',
        'link',
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
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
