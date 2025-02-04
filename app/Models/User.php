<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'location',
        'birth_date',
        'national_id',
        'avatar',
        'card_images',
        'neighborhood_id',
        'region_id',
        'city_id',
        'registration_confirmed',
        'national_card_image_front',
        'national_card_image_back',
        'mobile_number_visibility',
        'birthdate_visibility',
        'email_visibility',
        'show_no_complainted_posts',
        'admin',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',

    ];
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id');
    }
}
