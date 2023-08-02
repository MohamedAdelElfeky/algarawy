<?php

use App\Http\Controllers\Api\ApiBankAccountController;
use App\Http\Controllers\Api\ApiCourseController;
use App\Http\Controllers\Api\ApiJobController;
use App\Http\Controllers\Api\ApiMeetingController;
use App\Http\Controllers\Api\ApiProjectController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\CityController;
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
        'p-jobs' => ApiJobController::class, // jobs Resource
        'p-courses' => ApiCourseController::class, // courses Resource
        'p-meetings' => ApiMeetingController::class, // meetings Resource
        'p-bank-accounts' => ApiBankAccountController::class, // bank-accounts Resource
        'p-projects' => ApiProjectController::class,
        'p-discounts' => DiscountController::class,
        'p-services' => ServiceController::class,

    ]);

    // Bank related routes
    Route::prefix('bank')->group(function () {
        Route::get('/getSavings', [ApiBankAccountController::class, 'getSavings']);
        Route::get('/getCharities', [ApiBankAccountController::class, 'getCharities']);
    });
});
