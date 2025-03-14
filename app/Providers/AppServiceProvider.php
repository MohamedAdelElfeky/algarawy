<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use App\Domain\Repositories\CourseRepositoryInterface;
use App\Domain\Repositories\DashboardRepositoryInterface;
use App\Domain\Repositories\DiscountRepositoryInterface;
use App\Domain\Repositories\JobRepositoryInterface;
use App\Domain\Repositories\MeetingRepositoryInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Repositories\ServiceRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\EloquentCourseRepository;
use App\Infrastructure\EloquentDiscountRepository;
use App\Infrastructure\EloquentJobRepository;
use App\Infrastructure\EloquentMeetingRepository;
use App\Infrastructure\EloquentProjectRepository;
use App\Infrastructure\EloquentServiceRepository;
use App\Infrastructure\EloquentUserRepository;
use App\Infrastructure\Repositories\DashboardRepository;
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
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(MeetingRepositoryInterface::class, EloquentMeetingRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, EloquentServiceRepository::class);
        $this->app->bind(JobRepositoryInterface::class, EloquentJobRepository::class);
        $this->app->bind(DiscountRepositoryInterface::class, EloquentDiscountRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, EloquentCourseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
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
