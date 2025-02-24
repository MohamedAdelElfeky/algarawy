<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FilePdf extends Model
{
    use HasFactory;
    protected $fillable = ['url', 'type',  'mime'];

    public function pdfable(): MorphTo
    {
        return $this->morphTo();
    }
}
