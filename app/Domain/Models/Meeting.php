<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;


class Meeting extends Model
{
    protected $fillable = [
        'user_id', 'datetime', 'link', 'name',
        'start_time', 'end_time', 'description', 'type', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
