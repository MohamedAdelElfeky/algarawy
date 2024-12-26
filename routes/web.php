<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/request-otp', [PasswordResetController::class, 'requestOtpForm'])->name('password.request_otp');
Route::post('/request-otp', [PasswordResetController::class, 'sendOtp'])->name('password.send_otp');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verify_otp');

require __DIR__ . '/auth.php';

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('auth');
Route::middleware('auth')->group(function () {

    Route::get('/users', [UserController::class, 'getAllUsers'])->name('users.index');
    Route::post('/toggle-user/{id}', [UserController::class, 'toggleUser']);
    Route::get('/admin', [UserController::class, 'admin'])->name('admin');
    Route::post('/admin/add-user', [UserController::class, 'addUser'])->name('addUser');
    Route::get('/userActive', [UserController::class, 'userActive'])->name('userActive');
    Route::get('/userNotActive', [UserController::class, 'userNotActive'])->name('userNotActive');
    Route::post('/changePasswordByAdmin', [UserController::class, 'changePasswordByAdmin'])->name('password.update.admin');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jobs', JobController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('meetings', MeetingController::class);

    Route::resource('bank-accounts', BankAccountController::class);
    Route::post('/banks/activate/{id}', [BankAccountController::class, 'activate'])->name('banks.activate');
    Route::post('/banks/deactivate/{id}', [BankAccountController::class, 'deactivate'])->name('banks.deactivate');
    Route::get('/accountCharitySaving', [BankAccountController::class, 'accountCharitySaving'])->name('accountCharitySaving');
    Route::get('/accountInvestment', [BankAccountController::class, 'accountInvestment'])->name('accountInvestment');

    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support/add-or-update-number', [SupportController::class, 'addOrUpdateNumber'])->name('addOrUpdateSupport');

    Route::resource('regions', RegionController::class);

    Route::resource('cities', CityController::class);

    Route::resource('neighborhoods', NeighborhoodController::class);
    Route::put('addNeighborhoods', [NeighborhoodController::class, 'addNeighborhood'])->name('addNeighborhood');
    Route::put('/jobs/{job}/change-status', [JobController::class, 'changeStatus'])->name('jobs.changeStatus');
    Route::put('/discounts/{discount}/change-status', [DiscountController::class, 'changeStatus'])->name('discounts.changeStatus');
    Route::put('/discounts/{discount}/change-status', [DiscountController::class, 'changeStatus'])->name('discounts.changeStatus');
    Route::put('/courses/{course}/change-status', [CourseController::class, 'changeStatus'])->name('courses.changeStatus');
    Route::put('/projects/{project}/change-status', [CourseController::class, 'changeStatus'])->name('projects.changeStatus');
});

Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);
