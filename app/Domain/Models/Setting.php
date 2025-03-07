<?php

namespace App\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['key','value','type'];
  
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_settings')
            ->withPivot('value')
            ->withTimestamps();
    }

    public static function getActiveSettings()
    {
        return self::where('value', true)->get();
    }

    public static function getSettingsByType($type)
    {
        return self::where('key', $type)->get();
    }

}
