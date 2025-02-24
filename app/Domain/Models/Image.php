<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;
    protected $fillable = ['url', 'model_type','type', 'model_id', 'image_type', 'mime'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
