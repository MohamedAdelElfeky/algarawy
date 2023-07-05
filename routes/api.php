<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MeetingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'p-jobs' => JobController::class, // jobs Resource
    'p-courses' => CourseController::class, // courses Resource
    'p-meetings' => MeetingController::class, // meetings Resource
    'p-bank-accounts' => BankAccountController::class, // bank-accounts Resource
]);
