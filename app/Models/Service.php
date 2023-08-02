<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'images',
        'location',
        'user_id',
    ];
    protected $casts = [
        'images' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
