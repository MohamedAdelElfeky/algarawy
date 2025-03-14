<?php

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Model;

trait savingUserIdModelTrait
{
    public static function booted()
    {
        parent::booted();
        static::saving(function (Model $model) {
            if (auth()->check() && !$model->isDirty('user_id')) {
                $model->user_id = auth()->id();
            }
        });
    }
}
