<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserWebController;
use App\Http\Controllers\VisibilityController;
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


require __DIR__ . '/auth.php';

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('auth');
Route::middleware('auth')->group(function () {
   
    
    Route::get('/admin', [UserWebController::class, 'admin'])->name('admin');
    Route::post('/admin/add-user', [UserWebController::class, 'addUser'])->name('addUser');
    Route::get('/userActive', [UserWebController::class, 'userActive'])->name('userActive');
    Route::get('/userNotActive', [UserWebController::class, 'userNotActive'])->name('userNotActive');
    Route::post('/changePasswordByAdmin', [UserWebController::class, 'changePasswordByAdmin'])->name('changePasswordByAdmin');
    
    Route::post('/toggle-user/{id}', [UserController::class, 'toggleUser']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jobs', JobController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('web-courses', CourseController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('meetings', MeetingController::class);
    Route::resource('services', ServiceController::class);

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

    Route::resource('memberships', MembershipController::class);

    Route::get('/import', [ImportController::class, 'showForm'])->name('user.import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/user/{id}/make-admin', [UserController::class, 'makeAdmin'])->name('user.makeAdmin');
    Route::get('/download-example',  [ImportController::class, 'users_example'])->name('download.example');

    Route::put('/approve/{model}/{id}', [ApprovalController::class, 'updateApprovalStatus'])->name('approve.update');
    Route::put('/visibility/{model}/{id}', [VisibilityController::class, 'updateVisibilityStatus'])->name('visibility.update');

    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/messages/{conversationId}', [ChatController::class, 'getMessages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::post('/conversation', [ChatController::class, 'createConversation']);
    });
});

Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);
