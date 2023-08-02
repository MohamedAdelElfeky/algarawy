<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'region_id'];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }

}
