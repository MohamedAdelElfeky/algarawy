<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('location', function ($attribute, $value, $parameters, $validator) {
            $locationValues = explode(',', $value);
    
            if (count($locationValues) === 2) {
                $latitude = $locationValues[0];
                $longitude = $locationValues[1];
    
                // Check if latitude and longitude are valid numeric values.
                if (is_numeric($latitude) && is_numeric($longitude)) {
                    return true;
                }
            }
    
            return false;
        });
        // Update defaultStringLength
        Builder::defaultStringLength(191);

        KTBootstrap::init();
    }
}
