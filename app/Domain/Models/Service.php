<?php
namespace App\Domain\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['user_id', 'description', 'location', 'status'];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
