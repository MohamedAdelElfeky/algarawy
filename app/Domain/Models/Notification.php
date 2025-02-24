<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'message', 'read', 'read_at', 'notifiable_id', 'notifiable_type'];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        if (!$this->read) {
            $this->read = true;
            $this->read_at = now();
            $this->save();
        }
    }

    public function user()
    {
        return $this->belongsTo(\App\Domain\Models\User::class);
    }
}
