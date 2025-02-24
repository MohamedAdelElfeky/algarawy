<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FilePdf as ModelsFilePdf;
use App\Models\Image as ModelsImage;
use App\Models\User;

class Project extends Model
{
    protected $fillable = ['description', 'location', 'status', 'user_id'];

    public function images()
    {
        return $this->hasMany(ModelsImage::class);
    }

    public function pdfs()
    {
        return $this->hasMany(ModelsFilePdf::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
