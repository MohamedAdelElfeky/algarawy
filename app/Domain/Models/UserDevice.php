<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = ['user_id', 'device_id', 'notification_token', 'auth_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
