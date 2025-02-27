<?php

namespace App\Domain\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes, HasApiTokens, Notifiable;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'national_id',
        'email_verified_at',
        'remember_token'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',

    ];
    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function getSettingValue($key, $default = null)
    {
        $userSetting = $this->userSettings()->whereHas('setting', function ($query) use ($key) {
            $query->where('key', $key);
        })->first();

        if ($userSetting) {
            return $userSetting->value;
        }

        $globalSetting = Setting::where('key', $key)->first();

        return $globalSetting ? $globalSetting->value : $default;
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

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }
}
