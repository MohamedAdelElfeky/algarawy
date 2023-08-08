<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $table ='meetings';
    protected $fillable = [
        'datetime',
        'link',
        'name',
        'from',
        'to',
        'description',
        'type',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
