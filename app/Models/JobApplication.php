<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JobApplication extends Model
{
    use HasFactory;
    protected $table = 'job_applications';

    protected $fillable = [
        'user_id',
        'job_id',

    ];
    public function pdfs(): MorphMany
    {
        return $this->morphMany(FilePdf::class, 'pdfable');
    }
}
