<?php

use App\Http\Controllers\Api\ApiBankAccountController;
use App\Http\Controllers\Api\ApiCourseController;
use App\Http\Controllers\Api\ApiJobController;
use App\Http\Controllers\Api\ApiMeetingController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    ]);

    // Bank related routes
    Route::prefix('bank')->group(function () {
        Route::get('/getSavings', [ApiBankAccountController::class, 'getSavings']);
        Route::get('/getCharities', [ApiBankAccountController::class, 'getCharities']);
    });
});
