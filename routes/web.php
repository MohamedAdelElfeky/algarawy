<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
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

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

require __DIR__ . '/auth.php';

Route::resource('jobs', JobController::class);
Route::resource('courses', CourseController::class);
Route::resource('meetings', MeetingController::class);
Route::resource('bank-accounts', BankAccountController::class);

Route::view('/family', 'admin.family.index')->name('families');
Route::view('/discount', 'admin.discount.index')->name('discounts');
Route::view('/project', 'admin.project.index')->name('projects');
// Route::view('/course', 'admin.course.index')->name('courses');
Route::view('/type_of_relationships', 'admin.type_of_relationships.index')->name('type.of.relationships');
