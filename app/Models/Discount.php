<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $fillable = [
        'description',
        'images',
        'files',
        'location',
        'discount',
        'price',
        'user_id',
    ];
    protected $casts = [
        'images' => 'array',
        'files' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
