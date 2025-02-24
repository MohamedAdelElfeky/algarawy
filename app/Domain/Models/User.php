<?php

namespace App\Domain\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'national_id'
    ];

    protected $hidden = ['password'];

    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function getSettingValue($key, $default = null)
    {
        return $this->settings->where('key', $key)->first()->value ?? $default;
    }

    public function settings()
    {
        return $this->belongsToMany(Setting::class, 'user_settings')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id');
    }
}
