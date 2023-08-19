<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProjectController;
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

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'getAllUsers'])->name('users.index');
    Route::post('/toggle-user/{id}', [UserController::class, 'toggleUser']);
    Route::get('/admin', [UserController::class, 'admin'])->name('admin');
    Route::post('/admin/add-user', [UserController::class, 'addUser'])->name('addUser');
    Route::get('/userActive', [UserController::class, 'userActive'])->name('userActive');
    Route::get('/userNotActive', [UserController::class, 'userNotActive'])->name('userNotActive');
    Route::get('/accountCharitySaving', [BankAccountController::class, 'accountCharitySaving'])->name('accountCharitySaving');
    Route::get('/accountInvestment', [BankAccountController::class, 'accountInvestment'])->name('accountInvestment');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::post('/banks/activate/{id}', [BankAccountController::class, 'activate'])->name('banks.activate');
    Route::post('/banks/deactivate/{id}', [BankAccountController::class, 'deactivate'])->name('banks.deactivate');
    Route::resource('jobs', JobController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('meetings', MeetingController::class);
    Route::resource('bank-accounts', BankAccountController::class);
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support/add-or-update-number', [SupportController::class, 'addOrUpdateNumber'])->name('addOrUpdateSupport');
});
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified']);
Route::post('/save-family', [ProjectController::class, 'store'])->name('save.family');
Route::view('/projecta', 'admin.project.index')->name('projects');

Route::get('/getMeeting', [DashboardController::class, 'meeting'])->name('meet');

Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

require __DIR__ . '/auth.php';
