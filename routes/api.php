<?php

use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NeighborhoodController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::POST('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/regions', [RegionController::class, 'index']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/neighborhoods', [NeighborhoodController::class, 'index']);
Route::get('/regions/{region}/cities', [CityController::class, 'getCitiesByRegion']);
Route::get('/cities/{city}/neighborhoods', [NeighborhoodController::class, 'getNeighborhoodsByCity']);

Route::middleware('auth:sanctum')->group(function () {
    // User related routes
    Route::prefix('user')->group(function () {
        Route::get('/meetings', [UserController::class, 'getMeetings']);
        Route::get('/courses', [UserController::class, 'getCourses']);
        Route::get('/data', [UserController::class, 'getUser']);
    });

    Route::apiResources([
        'p-jobs' => JobController::class, // jobs Resource
        'p-courses' => CourseController::class, // courses Resource
        'p-meetings' => MeetingController::class, // meetings Resource
        'p-bank-accounts' => BankAccountController::class, // bank-accounts Resource
        'p-projects' => ProjectController::class,
        'p-discounts' => DiscountController::class,
        'p-services' => ServiceController::class,
        'p-job-application' => JobApplicationController::class,
    ]);

    Route::post('/p-add-favorite/{type}/{id}', [FavoriteController::class, 'toggleFavorite']);
    Route::post('/p-add-like/{type}/{id}', [LikeController::class, 'toggleLike']);

    Route::get('/user/favorites', [FavoriteController::class, 'getUserFavorites']);
    Route::get('/getDataDashboard', [DashboardController::class, 'getDataDashboard']);

    // Search 
    Route::get('/p-projectsSearch', [ProjectController::class, 'search']);
    Route::get('/p-coursesSearch', [CourseController::class, 'search']);
    Route::get('/p-meetingsSearch', [MeetingController::class, 'search']);
    Route::get('/p-jobsSearch', [JobController::class, 'search']);
    Route::get('/p-discountsSearch', [DiscountController::class, 'search']);
    Route::get('/p-servicesSearch', [ServiceController::class, 'search']);

    // Bank related routes
    Route::prefix('bank')->group(function () {
        Route::get('/getSavings', [BankAccountController::class, 'getSavings']);
        Route::get('/getCharities', [BankAccountController::class, 'getCharities']);
    });

    Route::put('/profile/{user}', [AuthController::class, 'updateProfile']);
    Route::get('/searchUser', [AuthController::class, 'searchUser']);

});
